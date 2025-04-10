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
            $request = Http::withToken(Session::get('auth_token') ?? '');
            
            // Log request details
            \Log::info("API Request: {$method} {$endpoint}", [
                'data' => $data,
                'has_token' => !empty(Session::get('auth_token'))
            ]);
            
            $response = $request->{$method}($this->baseUrl . $endpoint, $data);
            
            // Log raw response for debugging
            \Log::info("API Raw Response from {$endpoint}:", [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers()
            ]);
            
            // Jika status 401 (Unauthorized), redirect ke login
            if ($response->status() === 401) {
                Session::forget(['auth_token', 'user', 'roles']);
                redirect()->route('login')->send();
                return;
            }
            
            // Check if the response is valid JSON
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
            // Log error with trace
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