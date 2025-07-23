<?php
try {
    $pdo = new PDO('mysql:host=db;port=3306;dbname=db', 'db', 'db');
    echo "Connection successful\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
