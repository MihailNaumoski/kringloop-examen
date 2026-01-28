<?php
// Configuratie bestand - Kringloop Centrum Duurzaam

// Start sessie
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting (uitzetten in productie)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Tijdzone
date_default_timezone_set('Europe/Amsterdam');

// Database configuratie
define('DB_HOST', 'localhost');
define('DB_NAME', 'duurzaam');
define('DB_USER', 'root');
define('DB_PASS', '');

// Applicatie configuratie
define('APP_NAME', 'Kringloop Centrum Duurzaam');
define('APP_URL', 'http://localhost/kringloop');

// BTW percentage
define('BTW_PERCENTAGE', 21);

// Sessie timeout in minuten
define('SESSION_TIMEOUT', 30);

// Include infrastructure
require_once __DIR__ . '/../classes/Database.php';

// Include entity classes
require_once __DIR__ . '/../classes/Entity/Artikel.php';
require_once __DIR__ . '/../classes/Entity/Categorie.php';
require_once __DIR__ . '/../classes/Entity/Klant.php';
require_once __DIR__ . '/../classes/Entity/Voorraad.php';
require_once __DIR__ . '/../classes/Entity/Status.php';
require_once __DIR__ . '/../classes/Entity/Planning.php';
require_once __DIR__ . '/../classes/Entity/Verkoop.php';
require_once __DIR__ . '/../classes/Entity/Persoon.php';
require_once __DIR__ . '/../classes/Entity/Wagen.php';

// Include gebruiker classes
require_once __DIR__ . '/../classes/Gebruiker/Gebruiker.php';
require_once __DIR__ . '/../classes/Gebruiker/Directie.php';
require_once __DIR__ . '/../classes/Gebruiker/Magazijnmedewerker.php';
require_once __DIR__ . '/../classes/Gebruiker/Winkelpersoneel.php';
require_once __DIR__ . '/../classes/Gebruiker/Chauffeur.php';

// Include DAO classes
require_once __DIR__ . '/../classes/Dao/BaseDao.php';
require_once __DIR__ . '/../classes/Dao/GebruikerDao.php';
require_once __DIR__ . '/../classes/Dao/ArtikelDao.php';
require_once __DIR__ . '/../classes/Dao/CategorieDao.php';
require_once __DIR__ . '/../classes/Dao/KlantDao.php';
require_once __DIR__ . '/../classes/Dao/VoorraadDao.php';
require_once __DIR__ . '/../classes/Dao/StatusDao.php';
require_once __DIR__ . '/../classes/Dao/PlanningDao.php';
require_once __DIR__ . '/../classes/Dao/VerkoopDao.php';
require_once __DIR__ . '/../classes/Dao/PersoonDao.php';
require_once __DIR__ . '/../classes/Dao/WagenDao.php';

// Include auth
require_once __DIR__ . '/../classes/Auth/AuthManager.php';

// Database connectie
$db = Database::getInstance()->getConnection();

// Auth manager
$auth = new AuthManager();

// XSS bescherming helper
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// CSRF token genereren
function generateCsrfToken() {
    if(empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF token valideren
function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Datum formatteren
function formatDatum($datum) {
    return date('d-m-Y', strtotime($datum));
}

// Datum en tijd formatteren
function formatDatumTijd($datum) {
    return date('d-m-Y H:i', strtotime($datum));
}

// Prijs formatteren
function formatPrijs($prijs) {
    return number_format($prijs, 2, ',', '.');
}
