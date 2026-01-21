<?php

namespace Services;


use Exception;

/**
 * GoogleAuthService
 * Handles Google OAuth via native cURL (no external deps)
 */
class GoogleAuthService
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;

    private const AUTH_URL = 'https://accounts.google.com/o/oauth2/auth';
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const USERINFO_URL = 'https://www.googleapis.com/oauth2/v3/userinfo';

    public function __construct()
    {
        $appConfig = require __DIR__ . '/../../config/app.php';
        $config = $appConfig['google'];
        $this->clientId = $config['client_id'] ?? '';
        $this->clientSecret = $config['client_secret'] ?? '';
        $this->redirectUri = $config['redirect_uri'] ?? '';

        // Ensure full URL for redirect
        if (!empty($this->redirectUri) && !str_starts_with($this->redirectUri, 'http')) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
            $host = $_SERVER['HTTP_HOST'];
            $uri = ltrim($this->redirectUri, '/');

            // Getting base path from script name
            $scriptName = dirname($_SERVER['SCRIPT_NAME']);
            $basePath = rtrim($scriptName, '/');

            $this->redirectUri = $protocol . $host . $basePath . '/' . $uri;

            file_put_contents(__DIR__ . '/../../public/auth_debug.log', date('Y-m-d H:i:s') . " [GoogleAuthService] Constructed Redirect URI: " . $this->redirectUri . "\n", FILE_APPEND);
        } else {
            file_put_contents(__DIR__ . '/../../public/auth_debug.log', date('Y-m-d H:i:s') . " [GoogleAuthService] Raw Redirect URI: " . $this->redirectUri . "\n", FILE_APPEND);
        }
    }

    /**
     * Generate the Google Login URL
     */
    public function getAuthUrl(): string
    {
        if (empty($this->clientId)) {
            throw new Exception('Google Client ID not configured.');
        }

        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online',
            'prompt' => 'select_account'
        ];

        return self::AUTH_URL . '?' . http_build_query($params);
    }

    /**
     * Exchange Auth Code for Access Token
     */
    public function getAccessToken(string $code): string
    {
        if (empty($this->clientSecret)) {
            throw new Exception('Google Client Secret not configured.');
        }

        $params = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'code' => $code,
            'grant_type' => 'authorization_code'
        ];

        $ch = curl_init(self::TOKEN_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Local dev only!

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new Exception('Curl Error: ' . curl_error($ch));
        }
        curl_close($ch);

        $data = json_decode($response, true);

        if ($httpCode !== 200 || isset($data['error'])) {
            throw new Exception('Token Error: ' . ($data['error_description'] ?? $data['error'] ?? 'Unknown error'));
        }

        return $data['access_token'];
    }

    /**
     * Get User Info using Access Token
     */
    public function getUserInfo(string $accessToken): array
    {
        $ch = curl_init(self::USERINFO_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Local dev only!

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('Curl Error: ' . curl_error($ch));
        }
        curl_close($ch);

        return json_decode($response, true);
    }
}
