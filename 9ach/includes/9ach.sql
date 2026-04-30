-- 9ach E-Commerce Database Schema

CREATE DATABASE IF NOT EXISTS `9ach` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `9ach`;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at DATETIME DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insert sample admin user (password: admin123)
INSERT INTO users (name, email, password, is_admin) VALUES 
('Admin User', 'admin@9ach.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Insert sample products
INSERT INTO products (name, price, description, image) VALUES
('Minimalist Watch', 129.99, 'A sleek, modern timepiece with a clean dial and genuine leather strap. Perfect for everyday wear.', 'watch.jpg'),
('Leather Wallet', 59.99, 'Handcrafted genuine leather wallet with multiple card slots and bill compartments.', 'wallet.jpg'),
('Ceramic Vase', 45.00, 'Elegant handmade ceramic vase with a matte finish. Adds sophistication to any space.', 'vase.jpg'),
('Linen Throw Pillow', 35.00, 'Soft, breathable linen pillow cover in natural tones. Includes premium down insert.', 'pillow.jpg'),
('Wooden Desk Lamp', 89.99, 'Scandinavian-inspired desk lamp with adjustable arm and warm LED lighting.', 'lamp.jpg'),
('Canvas Tote Bag', 29.99, 'Durable cotton canvas tote with reinforced handles. Perfect for daily errands.', 'tote.jpg');

-- Note: For the admin password above, the hash is for 'admin123'
-- You can generate a new hash with: password_hash('your_password', PASSWORD_DEFAULT)