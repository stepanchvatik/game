<!DOCTYPE html>
<?php

try{
    $conn = new mysqli('localhost', 'root', 'pass', 'game');
}catch(mysqli_sql_exception $e) {
    echo nl2br("Error: Unable to connect to MySQL.\n");
    echo nl2br("Debugging errno: " . mysqli_connect_errno()."\n");
    echo nl2br("Debugging error: " . mysqli_connect_error()."\n");
}

$inventory = [];
$marketplace = [];

$result = $conn->query("SELECT * FROM inventory");
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $inventory[] = $row;
    }
}

$result = $conn->query("SELECT * FROM marketplace");
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $marketplace[] = $row;
    }
}

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Inventory</title>
    <link href="./assets/css/bootstrap.css" rel="stylesheet">
    <link href="./assets/css/style.css" rel="stylesheet">

</head>
<body>
<div class="container">
    <div class="player-section">
        <h2>Player</h2>
        <p>Credits: <span id="playerCredits">1000</span></p>
        <p>Attack: <span id="playerAttack">0</span></p>
        <p>Defence: <span id="playerDefence">0</span></p>
        <div class="inventory">
            <h3>Inventory</h3>
            <table class="table text-white">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Atk</th>
                        <th>Def</th>
                        <th>Sell price</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <img src="./assets/images/sword.png" class="preview" alt="Sword">
                        </td>
                        <td>Meč</td>
                        <td>10</td>
                        <td>5</td>
                        <td>200</td>
                        <td>
                            <form method="POST" action="controller.php">
                                <input type="hidden" name="action" value="sell">
                                <input type="hidden" name="item_id" value="1">
                                <button type="submit">Sell</button>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="marketplace-section">
        <h2>Marketplace</h2>
        <table class="table text-white">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Atk</th>
                    <th>Def</th>
                    <th>Price</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($marketplace as $item): ?>
                    <tr>
                        <td>
                            <img src="./assets/images/<?=$item['type'];?>.png" class="preview" alt="<?=$item['name'];?>">
                        </td>
                        <td><?php echo $item['name']; ?></td>
                        <td><?php echo $item['attack']; ?></td>
                        <td><?php echo $item['defence']; ?></td>
                        <td><?php echo $item['price']; ?></td>
                        <td>
                            <form method="POST" action="controller.php">
                                <input type="hidden" name="action" value="buy">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <button type="submit">Buy</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="./assets/js/jquery.js"></script>

<script src="./assets/js/bootstrap.js"></script>
</body>
</html>