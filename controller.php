<?php

require "./config.php";

// Connection to DB
try{
    $conn = new mysqli('localhost', 'root', 'pass', 'game');
}catch(mysqli_sql_exception $e) {
    echo nl2br("Error: Unable to connect to MySQL.\n");
    echo nl2br("Debugging errno: " . mysqli_connect_errno()."\n");
    echo nl2br("Debugging error: " . mysqli_connect_error()."\n");
}

try{

    $dibi = new \Dibi\Connection([
        'driver' => 'mysqli',
        'host' => 'localhost',
        'username' => 'root',
        'password' => 'pass',
        'database' => 'game'
    ]);
}catch(Exception $e){
    var_dump($e->getMessage());
    die();
}

/*
 * fetchAll - vrací array/pole všech řádků
 * fetch - vrací jeden řádek
 * fetchPairs - vrací pole s klíčem a hodnotou
 * fetchSingle - vrací jednu buňku
 * fetchAssoc - vrací asociativní pole (klíč => hodnota, klíč=>řádek)
 * fetchPairs s jedním sloupcem vrací array hodnot toho sloupce
 * ->XXX()
 * ->YYY
 */


$_SESSION["message"] = "";

if($_GET["action"] == "logout"){
    session_destroy();
    header("Location: sign.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $action = ($_POST["action"] == null) ? "" : $_POST["action"];
    if($_POST["action"] == null){
        $action = "";
    }else{
        $action = $_POST["action"];
    }

    $item_id = intval($_POST['item_id'] ?? 0);

    if ($action === 'buy') {
        buyItem($dibi, $item_id);
    } elseif ($action === 'sell') {
        sellItem($dibi, $item_id);
    }
}

function buyItem($dibi, $item_id) {
    // 1. Zkontrolujte, zda hráč má dostatek kreditů (SELECT dotaz z tabulky player kde id = 1)
    // 2. Pokud ano, odečtěte cenu položky z kreditů hráče (UPDATE dotaz, sloupec credit )
    // 3. Přidejte položku do inventáře hráče se sníženou cenou (INSERT INTO inventory - stejné sloupce jako v marketplace)
    // 4. Odstraňte položku z tržiště (DELETE FROM marketplace)

    //$credit = $conn->query("SELECT credit FROM player WHERE id = 1")->fetch_assoc();
    //VS
    $credit = $dibi->select("credit")->from("player")->where("id = ?",$_SESSION["user_id"])->fetchSingle();

    $item = $dibi->select("*")->from("marketplace")->where("id = ?", $item_id)->fetch();
    //VS
    /*
    $stmt = $conn->prepare("SELECT * FROM marketplace WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    */

    if($credit >= $item["price"]){

        $newCredit = $credit - $item["price"];
        $valuesToUpdate = [
            "credit" => $newCredit
        ];
        $dibi->update("player",$valuesToUpdate)->where("id = ?",$_SESSION["user_id"])->execute();

        /*
        $statement = $conn->prepare("UPDATE player SET credit = ? WHERE id = 1");
        $newCredit = $credit["credit"] - $item["price"];
        $statement->bind_param("i", $newCredit);
        $statement->execute();
        $statement->close();
    */

        //insert do inventáře
        unset($item["id"]);
        $dibi->insert("inventory",$item)->execute();
/*
        $statement = $conn->prepare("INSERT INTO inventory (name,category,type,attack,defence,price) VALUES (?,?,?,?,?,?)");
        $statement->bind_param("sssiii",$item["name"],$item["category"],$item["type"],$item["attack"],$item["defence"],$item["price"]);
        $statement->execute();
        $statement->close();
*/
        //delete z tržiště
        $dibi->delete("marketplace")->where("id = ?", $item_id)->execute();


        $_SESSION["message"] = "Bought item {$item["id"]} for {$item["price"]} credits";
    }else{
        $_SESSION["message"] = "Not enough credits";
    }
}

function sellItem($dibi, $item_id) {
    // 1. Zkontrolujte, zda položka existuje v inventáři (SELECT dotaz)
    // 2. Pokud ano, přidejte cenu položky k hráčovým kreditům (UPDATE dotaz)
    // 3. Odstraňte položku z inventáře (DELETE FROM inventory)
    // 4. Přidejte položku do tržiště (INSERT INTO marketplace)
    // 5. **Zvyšte cenu položky na tržišti o 10-20 %** (UPDATE dotaz)

    $credit = $dibi->select("credit")->from("player")->where("id = ?",$_SESSION["user_id"])->fetchSingle();

    $item = $dibi->select("*")->from("inventory")->where("id = ?", $item_id)->fetch();

    $newCredit = $credit + $item["price"];
    $valuesToUpdate = [
        "credit" => $newCredit
    ];
    $dibi->update("player",$valuesToUpdate)->where("id = ?",$_SESSION["user_id"])->execute();

    unset($item["id"]);
    $dibi->insert("marketplace",$item)->execute();
    //delete z tržiště
    $dibi->delete("inventory")->where("id = ?", $item_id)->execute();
}




header("Location: " . $_SERVER['HTTP_REFERER']);
?>
