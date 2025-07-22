<?php

namespace App\Controllers;

use App\Models\AuthModel;

class AuthController {
    private $authModel;

    public function __construct() {
        $this->authModel = new AuthModel();
    }

    public function login() {
        $username = $_POST['username'] ?? '';
        $token = $_POST['token'] ?? '';
        
        if (empty($username) || empty($token)) {
            return [
                'success' => false,
                'message' => 'Usuario y token son requeridos'
            ];
        }
        
        $authResult = $this->authModel->authenticate($username, $token);
        if ($authResult['success']) {
            $this->authModel->createSession($username, $authResult['expires']);
            return [
                'success' => true,
                'message' => 'Autenticación exitosa',
                'username' => $username
            ];
        }
        
        return $authResult;
    }

    public function logout() {
        $this->authModel->destroySession();
        return [
            'success' => true,
            'message' => 'Sesión cerrada'
        ];
    }

    public function createToken() {
        if (!$this->authModel->validateSession()) {
            return [
                'success' => false,
                'message' => 'No autorizado'
            ];
        }
        
        $username = $_POST['username'] ?? '';
        if (empty($username)) {
            return [
                'success' => false,
                'message' => 'Usuario requerido'
            ];
        }
        
        $token = $this->authModel->createToken($username);
        return [
            'success' => true,
            'message' => 'Token creado exitosamente',
            'token' => $token
        ];
    }

    public function isAuthenticated() {
        return $this->authModel->validateSession();
    }

    public function getCurrentUser() {
        return $this->authModel->getCurrentUser();
    }

    public function getTokens() {
        if (!$this->authModel->validateSession()) {
            return [
                'success' => false,
                'message' => 'No autorizado'
            ];
        }
        
        try {
            $tokens = $this->authModel->getAllTokens();
            return [
                'success' => true,
                'tokens' => $tokens
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function revokeToken() {
        if (!$this->authModel->validateSession()) {
            return [
                'success' => false,
                'message' => 'No autorizado'
            ];
        }
        
        $username = $_POST['username'] ?? '';
        if (empty($username)) {
            return [
                'success' => false,
                'message' => 'Usuario requerido'
            ];
        }
        
        try {
            $result = $this->authModel->revokeToken($username);
            return [
                'success' => $result,
                'message' => $result ? 'Token revocado correctamente' : 'Token no encontrado'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
?> 