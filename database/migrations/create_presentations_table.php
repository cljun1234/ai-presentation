<?php
// Simple migration script
require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = Database::getInstance();

    // Create presentations table
    $sql = "CREATE TABLE IF NOT EXISTS presentations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        topic VARCHAR(255) NOT NULL,
        filename VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (user_id)
    )";

    $pdo->exec($sql);
    echo "Table 'presentations' created successfully.\n";

} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage() . "\n");
}
