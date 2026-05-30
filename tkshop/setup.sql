-- Create Database
CREATE DATABASE IF NOT EXISTS tkshop;
USE tkshop;

-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(100),
    full_name VARCHAR(100),
    address TEXT,
    phone VARCHAR(20),
    role VARCHAR(20) DEFAULT 'user',
    balance DECIMAL(10,2) DEFAULT 1000.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200),
    description TEXT,
    price DECIMAL(10,2),
    stock INT DEFAULT 10,
    category VARCHAR(100),
    image VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT,
    quantity INT,
    total DECIMAL(10,2),
    status VARCHAR(50) DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reviews Table
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT,
    rating INT,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Messages Table
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    email VARCHAR(100),
    subject VARCHAR(200),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Sample Data
INSERT INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@tkshop.com', '21232f297a57a5a743894a0e4a801fc3', 'Administrator', 'admin'),
('john_doe', 'john@email.com', '5f4dcc3b5aa765d61d8327deb882cf99', 'John Doe', 'user'),
('jane_smith', 'jane@email.com', '5f4dcc3b5aa765d61d8327deb882cf99', 'Jane Smith', 'user');

INSERT INTO products (name, description, price, stock, category) VALUES
('Gaming Laptop Pro', 'High performance gaming laptop with RTX 4060', 1299.99, 15, 'Electronics'),
('Wireless Mouse', 'Ergonomic wireless mouse with RGB', 29.99, 50, 'Accessories'),
('Mechanical Keyboard', 'RGB mechanical keyboard with blue switches', 89.99, 30, 'Accessories'),
('USB-C Headphones', 'Noise cancelling wired headphones', 79.99, 25, 'Audio'),
('Smart Watch', 'Fitness tracker with heart rate monitor', 199.99, 20, 'Wearables');

INSERT INTO reviews (user_id, product_id, rating, comment) VALUES
(2, 1, 5, 'Amazing laptop! Great performance'),
(3, 2, 4, 'Good mouse but battery life could be better');