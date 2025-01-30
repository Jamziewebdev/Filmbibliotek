<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filmbibliotek</title>
    <link href="stilmall.css" rel="stylesheet">
</head>
<body>
    <div id="wrapper">
        <h1>Filmbibliotek</h1>
        <?php
        // Inkluderar fil med databasuppgifter
        require_once 'db_login.php';

        // Ansluter till databasen med PDO
        try {
            $pdo = new PDO($attr, $user, $pass, $opts);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Kasta ett undantag om anslutningen misslyckas
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }

        // Funktion som sanerar användarinmatning genom att ta bort 
        // inledande/avslutande mellanslag. Förhindrar oavsiktliga mellanslag 
        // som kan orsaka problem vid t.ex. jämföresler
        // Htmlspecialchars omvandlar specialtecken till motsvarande HTML-
        // entitet. Detta förhindrar att HTML och JavaScript exekveras i 
        // webbläsaren när indata visas.
        // 'Return' returnerar den modifierade och säkrade strängen
        function sanitizeInput($input) {
            return htmlspecialchars(trim($input));
        }

            
        // Funktionalitet för att ta bort en film
        //Kontrollerar om det finns en parameter i URL:en som heter 'delete'
        if (isset($_GET['delete'])) {
            // Tilldelar parametern delete till variabeln $id
            $id = $_GET['delete'];
            ?>
            <script>
            //Om man klickat bekräfta returneras confirm() true..
            if (confirm("Är du säker på att du vill radera? Åtgärden är permanent.")) {
                window.location.href = 
                // Omdirigeras användaren tillbaka till biblioteket med parametern 
                // 'confirmed_delete'
                "<?php echo $_SERVER['PHP_SELF'] . 
                '?confirmed_delete=' . $id; ?>";
            } else {
                // Om användaren klickar avbryt i dialogrutan skickas den tillbaka
                // till bibliotekssidan utan åtgärd.
                window.location.href = 
                "<?php echo $_SERVER['PHP_SELF']; ?>";
            }
            </script>
            <?php
        }

        // Hanterar bekräftelse av borttagning
        //'if (isset($GET.....)) kontrollerar om parametern 'confirmed_delete 
        // finns i URL:en. Om ja, körs koden innuti villkoret
        if (isset($_GET['confirmed_delete'])) {
            // Confirm delete tilldelas id:t på den valda filmen
            $id   = $_GET['confirmed_delete'];
            // Förbereder SQL-fråga om att radera filmen med det specifika id:t
            $stmt = $pdo->prepare("DELETE FROM film WHERE id = ?");
            // Slutför åtgärden
            $stmt->execute([$id]);
            // Hänvisa tillbaka till bibliotekssidan när radering är genomförd
            header("Location: {$_SERVER['PHP_SELF']}");
            // Avslutar körningen av PHP-scriptet
            exit();
        }

        // Hanterar redigering
        // Kontrollerar om 'edit' är valt
        if (isset($_GET['edit'])) {
            // Hämtar värdet på edit i URL:en
            $edit_id = $_GET['edit'];
            // Hämtar film baserat på valt id och förbereder SQL-fråga
            // för att hämta alla detaljer om filmen som matchar id från tabellen film
            $stmt = $pdo->prepare("SELECT * FROM film WHERE id = ?");
            // Kör frågan med det valda id:t
            $stmt->execute([$edit_id]);
            // Hämtar resultatet av den körda frågan och lagrar det i 
            // variabeln $edit_film som en associativ array
            $edit_film = $stmt->fetch(PDO::FETCH_ASSOC);
            // Visar formuläret för redigering av film
            ?>
            <!-- Öppnar ett html-formulär där data skickas tillbaka till samma 
            sida med hjälp av POST-metoden -->
            <form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
                <!-- Lägger till ett dolt input-fält som innehåller värdet för
                den valda filmen -->
                <input type="hidden" name="id" value="<?= $edit_id ?>">
                <pre>
                    <!-- Inmatning av titel, regissör och år via textfält.
                    Dessa textfält har försetts med patterns för att tillåta 
                    och förbjuda olika saker, exempelvis bokstäver på årtal.
                    Formuläret hämtar värdena för den film som ska redigeras och
                    lägger dessa i variabln $edit_film -->
                    <p><b>Title:</b></p><input type="text" name="title" 
                        pattern="[a-zA-Z0-9,.\-!?':; ]*" 
                        title=
                        "Endast stora och små bokstäver, siffror, kommatecken, 
                        punkt, bindestreck, mellanslag, utropstecken och 
                        frågetecken tillåtna" required
                        value="<?= $edit_film['title'] ?>">
                    <p><b>Director:</b></p><input type="text" name="director" 
                        pattern="[a-zA-Z ,-']*" title="Endast stora och 
                        små bokstäver,kommatecken, bindestreck och mellanslag 
                        tillåtna" required value="<?= $edit_film['director'] ?>">
                    <p><b>Year:</b></p><input type="text" name="year" pattern="\d{4}" 
                        title="Endast fyra siffror tillåtna" required
                        value="<?= trim($edit_film['year']) ?>">
                        <!-- Inmatning av kategori med hjälp av radioknappar. -->
                    <p><b>Category:</b></p><?php
                        // Hämtar alla värden i kategoritabellen
                        $query = "SELECT * FROM category";
                        $categories = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
                        // Går igenom alla kategorier med en foreach-loop och 
                        // tilldelar den ibockade kategorin variabeln $edit_film
                        foreach ($categories as $category) {
                            $category_id   = $category['id'];
                            $category_name = $category['namn'];
                            $checked       = ($category_id 
                            == $edit_film['category_id']) ? 'checked' : '';
                            // Skriver ut en radioknapp för varje kategori-
                            // alternativ, där den tidigare kategorin är förvald
                            echo "<br><input type='radio' name='category' 
                            title='Vänligen välj en kategori' required 
                            value='$category_id' $checked> $category_name ";
                    }
                    ?>
                    <!-- Knapp för att skicka in ändring -->
                    <input type="submit" name="update" value="Update Record">
                </pre>
            </form>
                <?php
                // Om redigera ej är iklickat...
            } else {
                // ..visas formulär för att lägga till ny film
                ?>
                <!-- Öppnar ett html-formulär där data skickas tillbaka till samma 
                sida med hjälp av POST-metoden -->
                <form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
                    <pre>
                        <!-- Inmatning av titel, regissör och år via textfält.
                        Dessa textfält har försetts med patterns för att 
                        tillåta och förbjuda olika saker, exempelvis 
                        bokstäver på årtal.-->
                        <p><b>Title:</b></p><input type="text" name="title" 
                            pattern="[a-zA-Z0-9,.\-!?': ]*" 
                            title="Endast stora och små bokstäver, siffror, 
                            kommatecken, punkt, bindestreck, mellanslag, 
                            apostrof, utropstecken och frågetecken tillåtna" 
                            required>
                        <p><b>Director:</b></p><input type="text" name="director" 
                            pattern="[a-zA-Z ,-']*" title="Endast stora och 
                            små bokstäver, kommatecken, bindestreck och 
                            mellanslag tillåtna" required>
                        <p><b>Year:</b></p><input type="text" name="year" 
                            pattern="\d{4}" title="Endast fyra siffror 
                            tillåtna" required>
                        <!-- Inmatning av kategori med hjälp av radioknappar. -->
                        <p><b>Category:</b></p><?php 
                            // Hämtar alla kategorier med en SQL-fråga
                            $query = "SELECT * FROM category";
                            $categories = 
                            $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($categories as $category) {
                                $category_id   = $category['id'];
                                $category_name = $category['namn'];
                                // Visar kategorierna med radioknappar
                                echo "<br><input type='radio' name='category' 
                                title='Vänligen välj en kategori' required  
                                value='$category_id'> $category_name ";
                        }
                        ?>
                    <!-- Knapp för att skicka in -->
                    <input type="submit" name="add" value="Add New">
                    </pre>
                </form>
                <?php
            }

            // Lägga till eller uppdatera film
            // Denna kontrollstruktur kollar om användaren har klickat på lägg
            // till, eller uppdatera. Om någon är sann körs koden innuti villkoret
            if (isset($_POST["add"]) || isset($_POST["update"])) {
                // Hanterar titeln, regissören och årtalet, och sanerar dem 
                // genom att ta bort inledande eller avslutande mellanslag, 
                // samt omvandlar specialtecken till html-entiteter för säkerhet. 
                $title    = sanitizeInput($_POST["title"]);
                $director = sanitizeInput($_POST["director"]);
                $year     = sanitizeInput($_POST["year"]);
                // Hämtar kategorin för den nya eller uppdaterade filmen
                // från formuläret. Eftersom kategorin inte behöver 
                // saneras (ingen användarinmatning som kan orsaka 
                // säkerhetsproblem), så används den direkt utan att saneras.
                $category = $_POST["category"];
                // Kontrollerar om titel, regissör och år har fyllts i av 
                // användaren,om ja körs koden innuti villkoret
                if ($title && $director && $year) {
                    // Kontrollerar om användaren har klickat på lägg till
                    if (isset($_POST["add"])) {
                        // Förbereder en SQL-fråga för att lägga till en ny rad
                        // i tabellen film
                        $stmt = $pdo->prepare("INSERT INTO film 
                        (title, director, year, category_id) VALUES (?, ?, ?, ?)");
                    // Annars om användaren har klickat i uppdatera så körs
                    // detta villkor
                    } elseif (isset($_POST["update"])) {
                        // Hämtar id:t för filmen som ska uppdateras och 
                        // sanerar på samma sätt som titeln
                        $id         = sanitizeInput($_POST["id"]);
                        // Förbereder SQL-fråga om att uppdatera befintlig rad
                        $stmt       = $pdo->prepare("UPDATE film SET 
                        title       = ?, 
                        director    = ?, 
                        year        = ?, 
                        category_id = ? 
                        WHERE id    = ?");
                        // Binder id:t till det femte frågetecknet i SQL-frågan
                        $stmt->bindParam(5, $id, PDO::PARAM_INT);
                    }
                    // Binder titel, regissör, årtal och kategori till respektive
                    // parameter i SQL-frågan
                    $stmt->bindParam(1, $title);
                    $stmt->bindParam(2, $director);
                    $stmt->bindParam(3, $year);
                    $stmt->bindParam(4, $category);
                    $stmt->execute();
                    // Omdirigerar tillbaka till biblioteket efter att ha 
                    //redigerat eller lagt till
                    header("Location: {$_SERVER['PHP_SELF']}");
                    exit();
                }
            }

            // Hämta och visa filmer
            // Förbereder en SQL-fråga för att välja all information
            // från tabellen "film", samt namn på kategorin från
            // tabellen "category"
            $query = "SELECT film.*, 
            category.namn AS category_name FROM film JOIN category ON 
            film.category_id = category.id";
            // Utför den förberedda SQL-frågan
            $result = $pdo->query($query);
            // Startar en while-loop som itererar över varje rad
            // i resultatet på frågan, och tilldelar information
            // till variabeln "row"
            while ($row   = $result->fetch()) {
                // Hämtar filmens id och tilldelar den variabeln $id
                $id       = $row['id'];
                // Samma som ovan gäller för titel, regissör och årtal
                // Men här har jag även lagt till htmlspecialchars som
                // sanerar specialtecken
                $title    = htmlspecialchars($row['title']);
                $director = htmlspecialchars($row['director']);
                $year     = htmlspecialchars($row['year']);
                // Även kategori har samma som ovan, men sanerar inte 
                // specialtecken eftersom det inte är någon egen användar-
                // input utan knappar istället
                $category = $row['category_name'];
                // Använder heredoc-syntax för att kunna skapa en fler-radig
                // sträng med html-kod. På så vis kan variabler inkluderas i
                // strängen utan att använda dubbla citationstecken eller 
                // punktoperatorer
                echo <<<HTML
                    <p><b>Title:</b> $title <br><b>Director:</b> $director<br><b>Year:</b> $year<br><b>Category:</b> $category</p>
                    <!-- Skapar behållare för knappar -->
                    <div id="buttons">
                        <!-- Skapar länkar för redigering och radering. Knapparna
                        innehåller id:t för aktuell film -->
                        <a href="{$_SERVER["PHP_SELF"]}?edit=$id">Edit</a>
                        <a href="{$_SERVER["PHP_SELF"]}?delete=$id">Delete</a><hr>
                    </div>
                HTML;
            }
            ?>
    </div>
</body>
</html>
