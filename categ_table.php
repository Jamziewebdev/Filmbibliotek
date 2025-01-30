<?php
require_once 'db_login.php';

try {
    $pdo = new PDO($attr, $user, $pass, $opts);
} catch (PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// SQL-fråga för att skapa tabellen 'category'
$query = "CREATE TABLE category  (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
)";

// Utför SQL-frågan för att skapa tabellen 'category'
$result = $pdo->query($query);

$categories = [
    'Action',
    'Drama',
    'Komedi',
    'Skräck',
    'Fantasy',
    'Svensk',
    'Science Fiction',
    'Dystopi',
];

foreach ($categories as $category) {
    add_category($pdo, $category);
}

function add_category($pdo, $category_name)
{
    $stmt = $pdo->prepare('INSERT INTO category (namn) VALUES(?)');
    $stmt->execute([$category_name]);
}

echo "Kategori tillagd";
?>
