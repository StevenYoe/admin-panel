<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class BaseController extends Controller
{
    /**
     * URL dasar API
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->baseUrl = config('app.api_base_url', env('API_BASE_URL'));
    }

    /**
     * Melakukan GET request ke API
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
     * Melakukan POST request ke API
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
     * Melakukan PUT request ke API
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
     * Melakukan DELETE request ke API
     *
     * @param string $endpoint
     * @return mixed
     */
    protected function apiDelete($endpoint)
    {
        return $this->apiRequest('delete', $endpoint);
    }

    /**
     * Melakukan HTTP request ke API
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return mixed
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
                    $requestOptions['multipart'][] = [
                        'name' => $key,
                        'contents' => $value
                    ];
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
            
            // Jika status 401 (Unauthorized), redirect ke login
            if ($response->status() === 401) {
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