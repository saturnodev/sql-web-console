-- Script de inicialización para MySQL
-- Se ejecuta automáticamente al crear el contenedor

-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS `sql_console` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos
USE `sql_console`;

-- Crear tabla de usuarios de ejemplo
CREATE TABLE IF NOT EXISTS `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL,
    `email` varchar(100) NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla de productos de ejemplo
CREATE TABLE IF NOT EXISTS `products` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `description` text,
    `price` decimal(10,2) NOT NULL,
    `stock` int(11) DEFAULT 0,
    `category` varchar(50) DEFAULT 'General',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_category` (`category`),
    KEY `idx_price` (`price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla de pedidos de ejemplo
CREATE TABLE IF NOT EXISTS `orders` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `total_amount` decimal(10,2) NOT NULL,
    `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_status` (`status`),
    KEY `idx_created_at` (`created_at`),
    CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar datos de ejemplo
INSERT INTO `users` (`username`, `email`) VALUES
('admin', 'admin@example.com'),
('john_doe', 'john@example.com'),
('jane_smith', 'jane@example.com'),
('bob_wilson', 'bob@example.com'),
('alice_brown', 'alice@example.com')
ON DUPLICATE KEY UPDATE `email` = VALUES(`email`);

INSERT INTO `products` (`name`, `description`, `price`, `stock`, `category`) VALUES
('Laptop Gaming Pro', 'Potente laptop para gaming con RTX 4080', 2499.99, 15, 'Electronics'),
('Smartphone Galaxy S23', 'Último modelo de Samsung con cámara de 200MP', 899.99, 25, 'Electronics'),
('Auriculares Wireless', 'Auriculares bluetooth con cancelación de ruido', 199.99, 50, 'Accessories'),
('Tablet iPad Pro', 'Tablet profesional con chip M2', 1099.99, 10, 'Electronics'),
('Teclado Mecánico', 'Teclado gaming con switches Cherry MX', 149.99, 30, 'Accessories'),
('Monitor 4K', 'Monitor de 27 pulgadas con resolución 4K', 399.99, 20, 'Electronics'),
('Mouse Gaming', 'Mouse RGB con 25,600 DPI', 79.99, 40, 'Accessories'),
('Webcam HD', 'Cámara web de 1080p con micrófono integrado', 89.99, 35, 'Accessories')
ON DUPLICATE KEY UPDATE 
    `description` = VALUES(`description`),
    `price` = VALUES(`price`),
    `stock` = VALUES(`stock`),
    `category` = VALUES(`category`);

INSERT INTO `orders` (`user_id`, `total_amount`, `status`) VALUES
(1, 2499.99, 'delivered'),
(2, 899.99, 'shipped'),
(3, 199.99, 'processing'),
(4, 1099.99, 'pending'),
(5, 149.99, 'delivered')
ON DUPLICATE KEY UPDATE 
    `total_amount` = VALUES(`total_amount`),
    `status` = VALUES(`status`);

-- Crear vistas útiles
CREATE OR REPLACE VIEW `user_orders_summary` AS
SELECT 
    u.username,
    u.email,
    COUNT(o.id) as total_orders,
    SUM(o.total_amount) as total_spent,
    MAX(o.created_at) as last_order_date
FROM users u
LEFT JOIN orders o ON u.id = o.user_id
GROUP BY u.id, u.username, u.email;

CREATE OR REPLACE VIEW `product_inventory` AS
SELECT 
    p.name,
    p.category,
    p.price,
    p.stock,
    CASE 
        WHEN p.stock = 0 THEN 'Out of Stock'
        WHEN p.stock < 10 THEN 'Low Stock'
        ELSE 'In Stock'
    END as stock_status
FROM products p
ORDER BY p.stock ASC;

-- Crear procedimientos almacenados útiles
DELIMITER //

CREATE PROCEDURE IF NOT EXISTS `GetUserOrders`(IN user_id_param INT)
BEGIN
    SELECT 
        o.id,
        o.total_amount,
        o.status,
        o.created_at,
        u.username
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.user_id = user_id_param
    ORDER BY o.created_at DESC;
END //

CREATE PROCEDURE IF NOT EXISTS `UpdateProductStock`(
    IN product_id_param INT,
    IN quantity_param INT
)
BEGIN
    UPDATE products 
    SET stock = stock + quantity_param
    WHERE id = product_id_param;
    
    SELECT 
        id,
        name,
        stock
    FROM products 
    WHERE id = product_id_param;
END //

CREATE PROCEDURE IF NOT EXISTS `GetLowStockProducts`(IN threshold INT)
BEGIN
    SELECT 
        id,
        name,
        category,
        price,
        stock
    FROM products 
    WHERE stock <= threshold
    ORDER BY stock ASC;
END //

DELIMITER ;

-- Crear triggers para auditoría
CREATE TABLE IF NOT EXISTS `audit_log` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `table_name` varchar(50) NOT NULL,
    `action` enum('INSERT','UPDATE','DELETE') NOT NULL,
    `record_id` int(11),
    `old_values` json,
    `new_values` json,
    `timestamp` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_table_action` (`table_name`, `action`),
    KEY `idx_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELIMITER //

CREATE TRIGGER IF NOT EXISTS `users_audit_insert` 
AFTER INSERT ON `users`
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, action, record_id, new_values)
    VALUES ('users', 'INSERT', NEW.id, JSON_OBJECT('username', NEW.username, 'email', NEW.email));
END //

CREATE TRIGGER IF NOT EXISTS `users_audit_update` 
AFTER UPDATE ON `users`
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, action, record_id, old_values, new_values)
    VALUES ('users', 'UPDATE', NEW.id, 
            JSON_OBJECT('username', OLD.username, 'email', OLD.email),
            JSON_OBJECT('username', NEW.username, 'email', NEW.email));
END //

CREATE TRIGGER IF NOT EXISTS `users_audit_delete` 
AFTER DELETE ON `users`
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, action, record_id, old_values)
    VALUES ('users', 'DELETE', OLD.id, JSON_OBJECT('username', OLD.username, 'email', OLD.email));
END //

DELIMITER ;

-- Mostrar mensaje de confirmación
SELECT 'Base de datos SQL Console inicializada correctamente' as message; 