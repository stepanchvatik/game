# Herní Inventář - Školní Projekt

## Popis Projektu
Tento projekt je jednoduchá webová aplikace pro správu herního inventáře. Umožňuje hráčům nakupovat a prodávat předměty na tržišti, spravovat svůj inventář a sledovat své kredity.

## Struktura Databáze
Projekt používá MySQL databázi se třemi hlavními tabulkami:
- `player` - informace o hráči (kredity)
- `inventory` - předměty ve vlastnictví hráče
- `marketplace` - předměty dostupné na tržišti

## Úkoly pro Studenty

### 1. Implementace Dibi
1. Instalace Composeru:
   ```bash
   curl -sS https://getcomposer.org/installer | php
   mv composer.phar /usr/local/bin/composer
   ```

2. Instalace Dibi:
   ```bash
   composer require dibi/dibi
   ```

3. Příklad použití Dibi:
   ```php
   // Připojení k databázi
   $dibi = new \Dibi\Connection([
       'driver' => 'mysqli',
       'host' => 'localhost',
       'username' => 'root',
       'password' => 'pass',
       'database' => 'game'
   ]);

   // SELECT příklad
   $result = $dibi->query('SELECT * FROM player WHERE id = %i', 1);

   // INSERT příklad
   $dibi->insert('inventory', [
       'name' => 'Meč',
       'category' => 'weapon',
       'type' => 'sword',
       'attack' => 10,
       'defence' => 5,
       'price' => 200
   ]);

   // UPDATE příklad
   $dibi->update('player', [
       'credit' => 300
   ])->where('id = %i', 1)->execute();

   // DELETE příklad
   $dibi->query('DELETE FROM marketplace WHERE id = %i', 1);
   ```

4. Úkol: Přepracujte funkce `buyItem` a `sellItem` v souboru `controller.php` pomocí Dibi.

### 2. Implementace Uživatelského Systému
1. Modifikace tabulky `player`:
   ```sql
   ALTER TABLE player
   ADD COLUMN username VARCHAR(255) NOT NULL,
   ADD COLUMN password VARCHAR(255) NOT NULL;
   ```

2. Vložení testovacích uživatelů:
   ```sql
   INSERT INTO player (username, password, credit) VALUES
   ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 500),
   ('user1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 300),
   ('user2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 200);
   ```
   (Všechna hesla jsou nastavena na "password")

3. Úkol: Vytvořte přihlašovací formulář v `index.php` a implementujte autentizaci:
   - Použijte `password_verify()` pro ověření hesla
   - Uložte ID uživatele do session s expirací 30 minut
   - Zobrazte přihlašovací formulář pouze nepřihlášeným uživatelům

### 3. Implementace Zabezpečení

#### Úkoly pro Implementaci Zabezpečení

1. Upravte přihlašovací systém tak, aby:
   - Počítal neúspěšné pokusy o přihlášení
   - Blokoval uživatele na 5 minut po 3 neúspěšných pokusech
   - Resetoval počet neúspěšných pokusů po úspěšném přihlášení

2. Implementujte blokování IP adres:
   - Po 6 neúspěšných pokusech zablokujte IP adresu
   - Ukládejte blokované IP adresy do souboru BLOCKED_IPS.txt
   - Kontrolujte blokované IP adresy před každým pokusem o přihlášení

3. Přidejte fingerprint uživatele:
   - Generujte unikátní fingerprint pro každou session
   - Kontrolujte fingerprint při každém požadavku
   - Pokud se fingerprint nezhoduje, odhlaste uživatele

#### Jednotlivé kroky

1. Rozšíření tabulky player:
   ```sql
   ALTER TABLE player
   ADD COLUMN failed_logins INT DEFAULT 0,
   ADD COLUMN blocked_until DATETIME DEFAULT NULL;
   ```

2. Implementace blokování uživatele:
   ```php
   // Při neúspěšném přihlášení
   $dibi->update('player', [
       'failed_logins' => $dibi->literal('failed_logins + 1')
   ])->where('id = ?', $user['id'])->execute();

   // Kontrola počtu neúspěšných pokusů
   if ($user['failed_logins'] >= 3) {
       $dibi->update('player', [
           'blocked_until' => date("Y-m-d H:i:s", strtotime('+5 minutes'))
       ])->where('id = ?', $user['id'])->execute();
   }

   // Při úspěšném přihlášení
   $dibi->update('player', [
       'failed_logins' => 0,
       'blocked_until' => null
   ])->where('id = ?', $user['id'])->execute();
   ```

3. Implementace blokování IP adres:
   ```php
   // Získání IP uživatele
   $ip = $_SERVER['REMOTE_ADDR'];
   
   // Funkce pro blokování IP
   function blockIP($ip) {
       $blockedIPs = file_get_contents('BLOCKED_IPS.txt');
       if (strpos($blockedIPs, $ip) === false) {
           file_put_contents('BLOCKED_IPS.txt', $ip . PHP_EOL, FILE_APPEND);
       }
   }

   // Kontrola blokované IP
   function isIPBlocked($ip) {
       $blockedIPs = file_get_contents('BLOCKED_IPS.txt');
       return strpos($blockedIPs, $ip) !== false;
   }
   ```

4. Implementace fingerprintu uživatele:
   ```php
   // Generování fingerprintu
   function generateFingerprint() {
       $userAgent = $_SERVER['HTTP_USER_AGENT'];
       $ip = $_SERVER['REMOTE_ADDR'];
       $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
       return hash('sha256', $userAgent . $ip . $acceptLanguage);
   }

   // Uložení fingerprintu do session
   $_SESSION['fingerprint'] = generateFingerprint();

   // Kontrola fingerprintu
   function checkFingerprint() {
       return $_SESSION['fingerprint'] === generateFingerprint();
   }
   
   // Zrušení session
   if (!checkFingerprint()) {
       // možný pokus o únos session – zneplatnit přihlášení
       session_destroy();
       die("Session fingerprint mismatch");
   }
   ```



