<?php

// Connection to DB
try{
    $conn = new mysqli('localhost', 'root', 'pass', 'game');
}catch(mysqli_sql_exception $e) {
    echo nl2br("Error: Unable to connect to MySQL.\n");
    echo nl2br("Debugging errno: " . mysqli_connect_errno()."\n");
    echo nl2br("Debugging error: " . mysqli_connect_error()."\n");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $item_id = intval($_POST['item_id'] ?? 0);

    if ($action === 'buy') {
        buyItem($conn, $item_id);
    } elseif ($action === 'sell') {
        sellItem($conn, $item_id);
    }
}

function buyItem($conn, $item_id) {
    // 1. Zkontrolujte, zda hráč má dostatek kreditů (SELECT dotaz z tabulky player kde id = 1)
    // 2. Pokud ano, odečtěte cenu položky z kreditů hráče (UPDATE dotaz, sloupec credit )
    // 3. Přidejte položku do inventáře hráče se sníženou cenou (INSERT INTO inventory - stejné sloupce jako v marketplace)
    // 4. Odstraňte položku z tržiště (DELETE FROM marketplace)
    $credit = $conn->query("SELECT credit FROM player WHERE id = 1")->fetch_assoc();

    $stmt = $conn->prepare("SELECT * FROM marketplace WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

function sellItem($conn, $item_id) {
    // 1. Zkontrolujte, zda položka existuje v inventáři (SELECT dotaz)
    // 2. Pokud ano, přidejte cenu položky k hráčovým kreditům (UPDATE dotaz)
    // 3. Odstraňte položku z inventáře (DELETE FROM inventory)
    // 4. Přidejte položku do tržiště (INSERT INTO marketplace)
    // 5. **Zvyšte cenu položky na tržišti o 10-20 %** (UPDATE dotaz)
}

header("Location: " . $_SERVER['HTTP_REFERER']);
?>
