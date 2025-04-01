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
   $dibi = new Connection([
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

## Požadavky na Implementaci
1. Všechny SQL dotazy musí být provedeny pomocí Dibi
2. Hesla musí být bezpečně hashována pomocí `password_hash()`
3. Session musí mít nastavenou expiraci 30 minut
4. Formuláře musí obsahovat základní validaci
5. Kód musí být přehledný a dobře komentovaný

## Hodnocení
- Správná implementace Dibi (30 bodů)
- Funkční nákup a prodej předmětů (20 bodů)
- Implementace uživatelského systému (30 bodů)
- Kvalita kódu a dokumentace (20 bodů)

## Tipy pro Implementaci
1. Použijte prepared statements pro všechny SQL dotazy
2. Implementujte základní error handling
3. Použijte CSS pro stylování přihlašovacího formuláře
4. Přidejte odhlášení uživatele
5. Implementujte základní zabezpečení proti SQL injection

## Deadline
Projekt musí být odevzdán do konce školního roku. 