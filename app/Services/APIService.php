<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class ApiService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = config('services.api.url');
        $this->token = session('api_token');
    }

    /**
     * Set the authorization token
     *
     * @param string $token
     * @return $this
     */
    public function withToken(string $token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Make an HTTP GET request
     *
     * @param string $endpoint
     * @param array $params
     * @return mixed
     */
    public function get(string $endpoint, array $params = [])
    {
        $response = Http::withToken($this->token)
            ->get($this->baseUrl . $endpoint, $params);

        return $this->handleResponse($response);
    }

    /**
     * Make an HTTP POST request
     *
     * @param string $endpoint
     * @param array $data
     * @return mixed
     */
    public function post(string $endpoint, array $data = [])
    {
        $response = Http::withToken($this->token)
            ->post($this->baseUrl . $endpoint, $data);

        return $this->handleResponse($response);
    }

    /**
     * Make an HTTP PUT request
     *
     * @param string $endpoint
     * @param array $data
     * @return mixed
     */
    public function put(string $endpoint, array $data = [])
    {
        $response = Http::withToken($this->token)
            ->put($this->baseUrl . $endpoint, $data);

        return $this->handleResponse($response);
    }

    /**
     * Make an HTTP DELETE request
     *
     * @param string $endpoint
     * @return mixed
     */
    public function delete(string $endpoint)
    {
        $response = Http::withToken($this->token)
            ->delete($this->baseUrl . $endpoint);

        return $this->handleResponse($response);
    }

    /**
     * Handle the HTTP response
     *
     * @param \Illuminate\Http\Client\Response $response
     * @return mixed
     */
    protected function handleResponse($response)
    {
        $json = $response->json();

        // Handle authentication errors
        if ($response->status() === 401) {
            Session::forget('api_token');
            return redirect()->route('login')->with('error', 'Your session has expired. Please login again.');
        }

        // Handle validation errors
        if ($response->status() === 422) {
            return $json;
        }

        // Handle server errors
        if ($response->failed()) {
            return [
                'success' => false,
                'message' => $json['message'] ?? 'An error occurred with the API',
                'errors' => $json['errors'] ?? []
            ];
        }

        return $json;
    }

    /**
     * Authenticate user and store token
     *
     * @param string $email
     * @param string $password
     * @return array
     */
    public function login(string $email, string $password)
    {
        $response = Http::post($this->baseUrl . '/login', [
            'email' => $email,
            'password' => $password
        ]);

        $json = $response->json();

        if ($response->successful() && isset($json['data']['token'])) {
            // Store token in session
            Session::put('api_token', $json['data']['token']);
            Session::put('user', $json['data']['user']);
            Session::put('roles', $json['data']['roles']);
            
            $this->token = $json['data']['token'];
        }

        return $json;
    }

    /**
     * Logout user and remove token
     *
     * @return array
     */
    public function logout()
    {
        $response = Http::withToken($this->token)
            ->post($this->baseUrl . '/logout');

        Session::forget('api_token');
        Session::forget('user');
        Session::forget('roles');

        return $response->json();
    }
}
