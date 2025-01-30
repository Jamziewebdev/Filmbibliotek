<?php
    // Databasuppgifter
    // Värdnamn för databasen (oftast 'localhost')
    $host = 'localhost'; 
    // Databasnamn
    $data = 'filmbibliotek'; 
    // Användarnamn för att ansluta till databasen
    $user = 'edit'; 
    // Lösenord för att ansluta till databasen
    $pass = 'password'; 
    // Teckenuppsättning för databasen
    $chrs = 'utf8mb4'; 

    // PDO-attribut och inställningar
    $attr = "mysql:host=$host;dbname=$data;charset=$chrs"; 
    $opts = [
        // Aktivera felhantering med undantag
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
        // Ange standard hämtningsläge för resultatsatser
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
        // Avaktivera efterlikning av förberedda uttalanden
        PDO::ATTR_EMULATE_PREPARES   => false, 
    ];
?>
