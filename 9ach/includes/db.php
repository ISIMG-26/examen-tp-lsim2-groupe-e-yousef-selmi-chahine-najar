<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', '9ach');

function generateCompatBcryptHash($password, $cost) {
    $cost = max(4, min(31, (int)$cost));
    $saltCharset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789./';
    $saltBody = '';

    for ($i = 0; $i < 22; $i++) {
        $saltBody .= $saltCharset[mt_rand(0, strlen($saltCharset) - 1)];
    }

    $salt = '$2y$' . str_pad((string)$cost, 2, '0', STR_PAD_LEFT) . '$' . $saltBody;
    return crypt($password, $salt);
}

function bootstrapSchemaIfNeeded($pdo) {
    $queries = array(
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            is_admin TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            description TEXT,
            image VARCHAR(255),
            created_at DATETIME DEFAULT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            total_price DECIMAL(10,2) NOT NULL,
            status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        ) ENGINE=InnoDB"
    );

    foreach ($queries as $sql) {
        $pdo->exec($sql);
    }

    // Seed/repair default admin credentials (password: admin123).
    $adminStmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
    $adminStmt->execute(array('admin@9ach.com'));
    $admin = $adminStmt->fetch();
    $legacyWrongSeedHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

    if (!$admin) {
        $insertAdmin = $pdo->prepare("INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 1)");
        $insertAdmin->execute(array(
            'Admin User',
            'admin@9ach.com',
            generateCompatBcryptHash('admin123', 10)
        ));
    } elseif ($admin['password'] === $legacyWrongSeedHash) {
        $updateAdmin = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $updateAdmin->execute(array(
            generateCompatBcryptHash('admin123', 10),
            $admin['id']
        ));
    }
}

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // If the DB does not exist yet, create it automatically.
    if ((int)$e->getCode() === 1049) {
        try {
            $bootstrapPdo = new PDO(
                "mysql:host=" . DB_HOST . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );

            $bootstrapPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $bootstrapError) {
            die("Connection failed: " . $bootstrapError->getMessage());
        }
    } else {
        die("Connection failed: " . $e->getMessage());
    }
}

try {
    bootstrapSchemaIfNeeded($pdo);
} catch (PDOException $schemaError) {
    die("Schema bootstrap failed: " . $schemaError->getMessage());
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
