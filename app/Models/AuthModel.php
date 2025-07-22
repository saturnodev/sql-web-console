<?php

namespace App\Models;

use App\Config\App;
use Exception;

class AuthModel {
    private $config;
    private $tokensFile;

    public function __construct() {
        $this->config = App::getConfig();
        $this->tokensFile = __DIR__ . '/../../data/tokens.json';
        $this->ensureTokensFileExists();
    }

    private function ensureTokensFileExists() {
        $dataDir = dirname($this->tokensFile);
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0755, true);
        }
        
        if (!file_exists($this->tokensFile)) {
            file_put_contents($this->tokensFile, json_encode([]));
            chmod($this->tokensFile, 0600); // Solo lectura/escritura para el propietario
        }
    }

    public function authenticate($username, $token) {
        $tokens = $this->loadTokens();
        
        if (isset($tokens[$username]) && $tokens[$username]['token'] === $token) {
            // Verificar si el token no ha expirado
            if (time() < $tokens[$username]['expires']) {
                return [
                    'success' => true,
                    'username' => $username,
                    'expires' => $tokens[$username]['expires']
                ];
            } else {
                // Token expirado, eliminarlo
                unset($tokens[$username]);
                $this->saveTokens($tokens);
            }
        }
        
        return ['success' => false, 'message' => 'Credenciales inválidas o token expirado'];
    }

    public function createToken($username) {
        $token = $this->generateSecureToken();
        $tokens = $this->loadTokens();
        
        $tokens[$username] = [
            'token' => $token,
            'created' => time(),
            'expires' => time() + ($this->config['TOKEN_EXPIRY_HOURS'] * 3600)
        ];
        
        $this->saveTokens($tokens);
        
        return $token;
    }

    public function revokeToken($username) {
        $tokens = $this->loadTokens();
        if (isset($tokens[$username])) {
            unset($tokens[$username]);
            $this->saveTokens($tokens);
            return true;
        }
        return false;
    }

    private function generateSecureToken() {
        return bin2hex(random_bytes(32));
    }

    private function loadTokens() {
        $content = file_get_contents($this->tokensFile);
        return json_decode($content, true) ?: [];
    }

    private function saveTokens($tokens) {
        file_put_contents($this->tokensFile, json_encode($tokens, JSON_PRETTY_PRINT));
    }

    public function validateSession() {
        session_start();
        
        if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
            return false;
        }
        
        // Verificar si la sesión no ha expirado
        if (isset($_SESSION['expires']) && time() > $_SESSION['expires']) {
            session_destroy();
            return false;
        }
        
        return true;
    }

    public function createSession($username, $expires) {
        session_start();
        $_SESSION['authenticated'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['expires'] = $expires;
    }

    public function destroySession() {
        session_start();
        session_destroy();
    }

    public function getCurrentUser() {
        session_start();
        return $_SESSION['username'] ?? null;
    }
}
?> 