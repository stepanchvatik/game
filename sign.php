<!DOCTYPE html>
<?php
require "./vendor/autoload.php";
session_start();

// Pokud je uživatel již přihlášen, přesměruj na index
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Zpracování přihlášení
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        $dibi = new \Dibi\Connection([
            'driver' => 'mysqli',
            'host' => 'localhost',
            'username' => 'root',
            'password' => 'pass',
            'database' => 'game'
        ]);
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (!empty($username) && !empty($password)) {
            try{

                $error = "Nesprávné přihlašovací údaje";
            }catch(Exception $e){
                $error = "Nevalidní databáze";
            }

        } else {
            $error = "Vyplňte všechna pole";
        }
        
    } catch (Exception $e) {

        $error = "Chyba při připojování k databázi";
    }
}
var_dump($error);
?>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Přihlášení - Herní Inventář</title>
    <link href="./assets/css/bootstrap.css" rel="stylesheet">
    <link href="./assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5" style="width:100%">
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Přihlášení</h3>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Uživatelské jméno</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Heslo</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Přihlásit se</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="./assets/js/jquery.js"></script>
    <script src="./assets/js/bootstrap.js"></script>
</body>
</html> 