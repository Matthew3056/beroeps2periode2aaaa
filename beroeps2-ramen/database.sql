-- Ramen Delivery Database - SQLite versie
-- Dit bestand is nu alleen voor referentie
-- De database wordt automatisch aangemaakt door includes/db.php

-- SQLite gebruikt andere syntax dan MySQL:
-- - INTEGER PRIMARY KEY AUTOINCREMENT (in plaats van AUTO_INCREMENT)
-- - TEXT (in plaats van VARCHAR/ENUM)
-- - REAL (in plaats van DECIMAL)
-- - CHECK constraints voor validatie
-- - DATETIME DEFAULT CURRENT_TIMESTAMP

-- Tabel: users
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    role TEXT NOT NULL DEFAULT 'user' CHECK(role IN ('user', 'admin')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabel: dishes
CREATE TABLE IF NOT EXISTS dishes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    price REAL NOT NULL,
    image TEXT DEFAULT 'default-ramen.jpg',
    available INTEGER DEFAULT 1 CHECK(available IN (0, 1))
);

-- Tabel: orders
CREATE TABLE IF NOT EXISTS orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    total REAL NOT NULL,
    status TEXT NOT NULL DEFAULT 'pending' CHECK(status IN ('pending', 'preparing', 'delivering', 'completed')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel: order_items
CREATE TABLE IF NOT EXISTS order_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER NOT NULL,
    dish_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL DEFAULT 1,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (dish_id) REFERENCES dishes(id) ON DELETE CASCADE
);

-- Test data wordt automatisch toegevoegd door includes/db.php
-- Admin account: username='admin', password='admin123'
