<?php
    // Inkludera filen med databasuppgifter
    require_once 'db_login.php';

    // Försök att skapa en PDO-anslutning till databasen
    try
    {
        $pdo = new PDO($attr, $user, $pass, $opts);
    }
    /* Fånga upp och hantera eventuella PDO-undantag som kan uppstå vid 
    anslutningen */
    catch (\PDOException $e)
    {
        // Kasta ett nytt PDO-undantag med samma meddelande och kod
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }

    // SQL-fråga för att skapa tabellen 'film'
    $query = "CREATE TABLE film (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        director VARCHAR(255),
        year  INT,
        category_id INT UNSIGNED,
        FOREIGN KEY (category_id) REFERENCES category(id)
    )";

    // Utför SQL-frågan för att skapa tabellen 'film'
    $result = $pdo->query($query);

    // Användaruppgifter för att lägga till en testfilm i tabellen 'film'
    $title      = 'Harry Potter and the Philosophers Stone';
    $director   = 'David Yates';
    $year       = '2001';

    // Hämta category-id för 'fantasy' från databasen
    $category_id = get_category_id($pdo, 'Fantasy');

    if ($category_id === false) {
        $category_id = add_category($pdo, 'Fantasy');
    }

    // Funktion för att lägga till en film i tabellen 'film'
    add_film($pdo, $title, $director, $year, $category_id);


    function get_category_id($pdo, $category_name)

    {
        $stmt = $pdo->prepare('SELECT id FROM category WHERE namn = ?');

        $stmt->execute([$category_name]);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ? $result['id'] : false;
    }

    function add_category($pdo, $category_name)
    {
        // Förbered ett SQL-uttryck för att lägga till en ny category i tabellen 'category'
        $stmt = $pdo->prepare('INSERT INTO category (namn) VALUES(?)');

        // Utför det förberedda SQL-uttrycket för att lägga till den nya category
        $stmt->execute([$category_name]);

        // Returnera det automatiskt tilldelade id för den nya category
        return $pdo->lastInsertId();
    }
    function add_film($pdo, $tl, $dr, $yr, $cid)
    {
        /* Förbered ett SQL-uttryck för att infoga filmuppgifter i 
        tabellen 'film' */
        $stmt = $pdo->prepare('INSERT INTO film (title, 
        director, year, category_id) VALUES(?, ?, ?, ?)');

        /* Binder parametrar till det förberedda SQL-uttrycket för att undvika 
        SQL-injektioner */
        $stmt->bindParam(1, $tl, PDO::PARAM_STR, 255);
        $stmt->bindParam(2, $dr, PDO::PARAM_STR, 255);
        $stmt->bindParam(3, $yr, PDO::PARAM_INT);
        $stmt->bindParam(4, $cid, PDO::PARAM_INT);
        
        /* Utför det förberedda SQL-uttrycket för att infoga användarens 
        uppgifter i tabellen 'users' */
        $stmt->execute([$tl, $dr, $yr, $cid]);
    }
?>