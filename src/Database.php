<?php

class Database {
    private $connection;
    private $config;

    public function __construct($config) {
        $this->config = $config;
        $this->connect();
    }

    private function connect() {
        try {
            $dsn = "mysql:host={$this->config['DB_HOST']};port={$this->config['DB_PORT']};dbname={$this->config['DB_NAME']};charset=utf8mb4";
            $this->connection = new PDO($dsn, $this->config['DB_USER'], $this->config['DB_PASSWORD'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            
            // Determinar si es SELECT o no
            if (stripos(trim($sql), 'SELECT') === 0) {
                return [
                    'type' => 'SELECT',
                    'data' => $stmt->fetchAll(),
                    'rowCount' => $stmt->rowCount()
                ];
            } else {
                return [
                    'type' => 'OTHER',
                    'rowCount' => $stmt->rowCount(),
                    'message' => 'Operación ejecutada correctamente'
                ];
            }
        } catch (PDOException $e) {
            throw new Exception("Error en la consulta: " . $e->getMessage());
        }
    }

    public function getTables() {
        $sql = "SHOW TABLES";
        $result = $this->query($sql);
        return array_column($result['data'], 'Tables_in_' . $this->config['DB_NAME']);
    }

    public function getTableStructure($tableName) {
        $sql = "DESCRIBE " . $tableName;
        return $this->query($sql);
    }

    public function isConnected() {
        return $this->connection !== null;
    }

    public function close() {
        $this->connection = null;
    }
}
?> 