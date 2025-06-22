-- Datenbank erstellen
DROP DATABASE IF EXISTS sportx;

CREATE DATABASE sportx CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE sportx;

-- Benutzer (für Login & Rollen)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    role ENUM('user', 'admin') DEFAULT 'user',
    status ENUM('active', 'banned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Produkte
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    price_text VARCHAR(50),
    image_main VARCHAR(255),
    marke VARCHAR(100),
    farbe VARCHAR(50),
    geschlecht ENUM('Herren', 'Damen', 'Unisex'),
    category VARCHAR(100),
    subcategory VARCHAR(100)
);

-- Produktbilder (zusätzliche Bilder)
CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    image_path VARCHAR(255),
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
);

-- Produktgrößen
CREATE TABLE product_sizes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    size VARCHAR(10),
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
);

-- Lagerbestand nach Größe
CREATE TABLE product_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    size VARCHAR(10),
    stock INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
);

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

-- Cart-Items
CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT,
    product_id INT,
    size VARCHAR(10),
    quantity INT DEFAULT 1,
    discount INT DEFAULT 0,
    gift TINYINT(1) DEFAULT 0,
    FOREIGN KEY (cart_id) REFERENCES cart (id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
);

-- Watchlists
CREATE TABLE IF NOT EXISTS watchlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

-- Watchlist-Items
CREATE TABLE IF NOT EXISTS watchlist_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    watchlist_id INT,
    product_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (watchlist_id) REFERENCES watchlists (id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
);

-- Bestellungen
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    status ENUM(
        'neu',
        'in_bearbeitung',
        'abgeschlossen',
        'abgelehnt',
        'storniert'
    ) DEFAULT 'neu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    admin_comment TEXT,
    FOREIGN KEY (user_id) REFERENCES users (id)
);

-- Order Items
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    size VARCHAR(10),
    quantity INT,
    price DECIMAL(10, 2),
    FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products (id)
);

-- Bewertungen
CREATE TABLE ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    user_id INT,
    stars INT CHECK (stars BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products (id),
    FOREIGN KEY (user_id) REFERENCES users (id)
);

-- Umfragen / Abstimmungen
CREATE TABLE polls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users (id)
);

CREATE TABLE poll_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poll_id INT,
    option_text VARCHAR(255),
    FOREIGN KEY (poll_id) REFERENCES polls (id) ON DELETE CASCADE
);

CREATE TABLE poll_votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    option_id INT,
    user_id INT,
    FOREIGN KEY (option_id) REFERENCES poll_options (id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

-- Retouren
CREATE TABLE returns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    user_id INT,
    reason TEXT,
    status ENUM(
        'beantragt',
        'genehmigt',
        'abgelehnt',
        'abgeschlossen'
    ) DEFAULT 'beantragt',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);