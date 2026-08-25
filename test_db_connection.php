<?php
try {
    $pdo = new PDO("mysql:host=db;port=3306;dbname=db", "db", "db");
    echo "Database connection successful!\n";
} catch(Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}
?>
