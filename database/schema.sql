-- Create database
CREATE DATABASE gift_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gift_management;

-- Create enterprises table
CREATE TABLE enterprises (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    contact_person VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Create users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    enterprise_id INT,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'distributor') DEFAULT 'distributor',
    limit_quantity INT DEFAULT 0,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (enterprise_id) REFERENCES enterprises(id)
) ENGINE=InnoDB;

-- Create master_products table
CREATE TABLE master_products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    unit VARCHAR(50),
    price DECIMAL(15,2),
    current_stock INT DEFAULT 0,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Create inventory table
CREATE TABLE inventory (
    id INT PRIMARY KEY AUTO_INCREMENT,
    enterprise_id INT,
    product_id INT,
    initial_quantity INT NOT NULL,
    additional_quantity INT DEFAULT 0,
    used_quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (enterprise_id) REFERENCES enterprises(id),
    FOREIGN KEY (product_id) REFERENCES master_products(id)
) ENGINE=InnoDB;

-- Create requests table
CREATE TABLE requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    enterprise_id INT,
    product_id INT,
    quantity INT NOT NULL,
    custom_request TEXT,
    status ENUM('pending', 'processing', 'shipping', 'completed') DEFAULT 'pending',
    recipient_name VARCHAR(255),
    recipient_phone VARCHAR(20),
    delivery_address TEXT,
    tracking_code VARCHAR(50),
    tracking_url VARCHAR(255),
    notes TEXT,
    created_by VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (enterprise_id) REFERENCES enterprises(id),
    FOREIGN KEY (product_id) REFERENCES master_products(id)
) ENGINE=InnoDB;

-- Insert default admin user (password: admin123)
INSERT INTO users (email, password, role) 
VALUES ('admin@quatang3a.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
