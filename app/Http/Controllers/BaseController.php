<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

// BaseController provides shared logic for all controllers in the admin panel.
// It centralizes API communication, role-based access control, and utility methods
// to ensure consistency and reduce code duplication across the application.

class BaseController extends Controller
{
    /**
     * The base URL for API requests, loaded from config or environment.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Constructor: Initializes the base API URL from configuration.
     */
    public function __construct()
    {
        $this->baseUrl = config('app.api_base_url', env('API_BASE_URL'));
    }

    /**
     * Check if the current user is a superadmin.
     *
     * Looks for the 'superadmin' role in the session's roles array.
     * Supports both array of strings and array of role objects.
     *
     * @return bool True if user is superadmin, false otherwise.
     */
    protected function isSuperAdmin()
    {
        $roles = Session::get('roles', []);
        
        // Check if 'superadmin' is in the roles array
        // This depends on how your roles are stored in the session
        // It could be an array of role names or an array of role objects
        
        // For array of strings
        if (in_array('superadmin', $roles)) {
            return true;
        }
        
        // For array of objects with 'role_name' property
        foreach ($roles as $role) {
            if (is_array($role) && isset($role['role_name']) && strtolower($role['role_name']) === 'superadmin') {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if the current user has permission to perform CRUD operations.
     *
     * If not a superadmin, flashes an error message and optionally redirects.
     *
     * @param string $route Route to redirect to if not authorized
     * @return bool True if authorized, otherwise redirects
     */
    protected function checkSuperAdminAccess($route = null)
    {
        if (!$this->isSuperAdmin()) {
            Session::flash('swal_type', 'error');
            Session::flash('swal_title', 'Access Denied');
            Session::flash('swal_msg', 'You do not have permission to perform this action. Only superadmins can modify data.');
            
            if ($route) {
                return Redirect::route($route);
            }
            
            return false;
        }
        
        return true;
    }

    /**
     * Send a GET request to the API.
     *
     * @param string $endpoint
     * @param array $params
     * @return mixed
     */
    protected function apiGet($endpoint, $params = [])
    {
        return $this->apiRequest('get', $endpoint, $params);
    }

    /**
     * Send a POST request to the API.
     *
     * @param string $endpoint
     * @param array $data
     * @return mixed
     */
    protected function apiPost($endpoint, $data = [])
    {
        return $this->apiRequest('post', $endpoint, $data);
    }

    /**
     * Send a PUT request to the API.
     *
     * @param string $endpoint
     * @param array $data
     * @return mixed
     */
    protected function apiPut($endpoint, $data = [])
    {
        return $this->apiRequest('put', $endpoint, $data);
    }

    /**
     * Send a DELETE request to the API.
     *
     * @param string $endpoint
     * @return mixed
     */
    protected function apiDelete($endpoint)
    {
        return $this->apiRequest('delete', $endpoint);
    }

    /**
     * Core method to send HTTP requests to the API.
     * Handles authentication, file uploads, error handling, and response parsing.
     * Logs requests and responses for debugging. Redirects to login on 401 errors (except for login endpoint).
     *
     * @param string $method HTTP method (get, post, put, delete)
     * @param string $endpoint API endpoint
     * @param array $data Request data or parameters
     * @return mixed API response as array or error structure
     */
    protected function apiRequest($method, $endpoint, $data = [])
    {
        try {
            // Token untuk autentikasi
            $token = Session::get('auth_token') ?? '';
            
            // Cek apakah ada file yang diupload
            $hasFiles = false;
            foreach ($data as $value) {
                if ($value instanceof \Illuminate\Http\UploadedFile) {
                    $hasFiles = true;
                    break;
                }
            }
            
            // Log request untuk debugging
            \Log::info("API Request: {$method} {$endpoint}", [
                'data' => $data,
                'has_token' => !empty($token),
                'has_files' => $hasFiles
            ]);
            
            // Handle file uploads berbeda dari request biasa
            if ($hasFiles) {
                // Create a new request instance with token
                $options = [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Accept' => 'application/json'
                    ]
                ];
                
                // Prepare multipart form data
                $formData = [];
                
                // Add regular form fields
                foreach ($data as $key => $value) {
                    if (!($value instanceof \Illuminate\Http\UploadedFile)) {
                        $formData[$key] = $value;
                    }
                }
                
                // For PUT requests, convert to POST with _method=PUT
                if (strtolower($method) === 'put') {
                    $actualMethod = 'post';
                    $formData['_method'] = 'PUT';
                } else {
                    $actualMethod = strtolower($method);
                }
                
                // Create a GuzzleHttp client for more control
                $client = new \GuzzleHttp\Client();
                
                // Prepare the request
                $requestOptions = [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Accept' => 'application/json'
                    ],
                    'multipart' => [],
                ];
                
                // Add form fields to multipart
                foreach ($formData as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $item) {
                            $requestOptions['multipart'][] = [
                                'name' => $key . '[]', // Tambahkan [] untuk array
                                'contents' => $item
                            ];
                        }
                    } else {
                        $requestOptions['multipart'][] = [
                            'name' => $key,
                            'contents' => $value
                        ];
                    }
                }
                
                // Add files to multipart
                foreach ($data as $key => $value) {
                    if ($value instanceof \Illuminate\Http\UploadedFile) {
                        $requestOptions['multipart'][] = [
                            'name' => $key,
                            'contents' => fopen($value->getRealPath(), 'r'),
                            'filename' => $value->getClientOriginalName()
                        ];
                    }
                }
                
                // Send the request
                $guzzleResponse = $client->request(
                    strtoupper($actualMethod), 
                    $this->baseUrl . $endpoint, 
                    $requestOptions
                );
                
                // Convert to Laravel response for consistency
                $response = new \Illuminate\Http\Client\Response(
                    new \GuzzleHttp\Psr7\Response(
                        $guzzleResponse->getStatusCode(),
                        $guzzleResponse->getHeaders(),
                        (string) $guzzleResponse->getBody()
                    )
                );
            } else {
                // Regular request tanpa file
                $response = Http::withToken($token)
                    ->acceptJson()
                    ->{$method}($this->baseUrl . $endpoint, $data);
            }
            
            // Log response untuk debugging
            \Log::info("API Raw Response from {$endpoint}:", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            // Modify this part - don't redirect for /login endpoint
            if ($response->status() === 401 && $endpoint !== '/login') {
                Session::forget(['auth_token', 'user', 'roles']);
                redirect()->route('login')->send();
                return;
            }
            
            // Periksa response JSON
            $responseData = $response->json();
            
            if ($responseData === null) {
                \Log::error("Invalid JSON response from {$endpoint}: " . $response->body());
                return [
                    'success' => false,
                    'message' => 'Invalid response format from server'
                ];
            }
            
            return $responseData;
        } catch (\Exception $e) {
            // Log error dengan trace
            \Log::error("API Request Error: {$method} {$endpoint}", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return error response
            return [
                'success' => false,
                'message' => 'Cannot connect to server: ' . $e->getMessage()
            ];
        }
    }
}