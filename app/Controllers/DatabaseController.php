<?php

namespace App\Controllers;

use App\Models\DatabaseModel;
use App\Models\AuthModel;

class DatabaseController {
    private $databaseModel;
    private $authModel;

    public function __construct() {
        $this->databaseModel = new DatabaseModel();
        $this->authModel = new AuthModel();
    }

    public function executeQuery() {
        if (!$this->authModel->validateSession()) {
            return [
                'success' => false,
                'message' => 'No autorizado'
            ];
        }
        
        $sql = $_POST['sql'] ?? '';
        if (empty($sql)) {
            return [
                'success' => false,
                'message' => 'Consulta SQL requerida'
            ];
        }
        
        try {
            $result = $this->databaseModel->query($sql);
            
            return [
                'success' => true,
                'data' => $result,
                'message' => 'Consulta ejecutada correctamente'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getTables() {
        if (!$this->authModel->validateSession()) {
            return [
                'success' => false,
                'message' => 'No autorizado'
            ];
        }
        
        try {
            $tables = $this->databaseModel->getTables();
            
            return [
                'success' => true,
                'tables' => $tables
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getTableStructure() {
        if (!$this->authModel->validateSession()) {
            return [
                'success' => false,
                'message' => 'No autorizado'
            ];
        }
        
        $tableName = $_POST['table'] ?? '';
        if (empty($tableName)) {
            return [
                'success' => false,
                'message' => 'Nombre de tabla requerido'
            ];
        }
        
        try {
            $structure = $this->databaseModel->getTableStructure($tableName);
            
            return [
                'success' => true,
                'structure' => $structure['data']
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function isDestructiveQuery($sql) {
        return $this->databaseModel->isDestructiveQuery($sql);
    }

    public function testConnection() {
        try {
            $isConnected = $this->databaseModel->isConnected();
            return [
                'success' => $isConnected,
                'message' => $isConnected ? 'Conexión exitosa' : 'Error de conexión'
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