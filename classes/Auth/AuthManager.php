<?php
// AuthManager - authenticatie en autorisatie beheer
class AuthManager {
    private $db;
    private $gebruikerDao;

    // Constructor
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->gebruikerDao = new GebruikerDao($this->db);
    }

    // Start sessie als nog niet actief
    public function startSession() {
        if(session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Inloggen
    public function login($username, $password) {
        $user = $this->gebruikerDao->verifyLogin($username, $password);
        if($user) {
            $this->startSession();
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['gebruikersnaam'] = $user->getGebruikersnaam();
            $_SESSION['rollen'] = $user->getRollen();
            $_SESSION['permissies'] = $user->getPermissies();
            $_SESSION['login_time'] = time();
            return true;
        }
        return false;
    }

    // Uitloggen
    public function logout() {
        $this->startSession();
        session_unset();
        session_destroy();
    }

    // Controleer of ingelogd
    public function isLoggedIn() {
        $this->startSession();
        return isset($_SESSION['user_id']);
    }

    // Haal huidige gebruiker
    public function getCurrentUser() {
        if($this->isLoggedIn()) {
            return $this->gebruikerDao->getById($_SESSION['user_id']);
        }
        return null;
    }

    // Haal gebruikers rol
    public function getRol() {
        $this->startSession();
        return $_SESSION['rollen'] ?? '';
    }

    // Controleer of gebruiker bepaalde rol heeft
    public function heeftRol($rol) {
        return $this->getRol() === $rol;
    }

    // Controleer of gebruiker permissie heeft
    public function heeftPermissie($permissie) {
        $this->startSession();
        $permissies = $_SESSION['permissies'] ?? [];
        return in_array($permissie, $permissies);
    }

    // Vereis ingelogd
    public function requireLogin() {
        if(!$this->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }

    // Vereis bepaalde rol
    public function requireRol($rol) {
        $this->requireLogin();
        if(!$this->heeftRol($rol)) {
            header('Location: index.php?error=geen_toegang');
            exit;
        }
    }

    // Vereis bepaalde permissie
    public function requirePermissie($permissie) {
        $this->requireLogin();
        if(!$this->heeftPermissie($permissie)) {
            header('Location: index.php?error=geen_toegang');
            exit;
        }
    }

    // Wijzig wachtwoord
    public function wijzigWachtwoord($userId, $oudWachtwoord, $nieuwWachtwoord) {
        $user = $this->gebruikerDao->getById($userId);
        if($user && password_verify($oudWachtwoord, $user->getWachtwoord())) {
            $data = [
                'gebruikersnaam' => $user->getGebruikersnaam(),
                'wachtwoord' => $nieuwWachtwoord,
                'rollen' => $user->getRollen(),
                'is_geverifieerd' => $user->getIsGeverifieerd()
            ];
            return $this->gebruikerDao->update($userId, $data);
        }
        return false;
    }

    // Controleer sessie timeout (30 minuten)
    public function checkTimeout($minutes = 30) {
        $this->startSession();
        if(isset($_SESSION['login_time'])) {
            $elapsed = time() - $_SESSION['login_time'];
            if($elapsed > ($minutes * 60)) {
                $this->logout();
                return true;
            }
        }
        return false;
    }

    // Ververs sessie tijd
    public function refreshSession() {
        $this->startSession();
        $_SESSION['login_time'] = time();
    }

    // Haal alle gebruikers op
    public function getAll() {
        return $this->gebruikerDao->getAll();
    }

    // Haal gebruiker op via ID
    public function getById($id) {
        return $this->gebruikerDao->getById($id);
    }

    // Maak nieuwe gebruiker
    public function create($data) {
        return $this->gebruikerDao->create($data);
    }

    // Update gebruiker
    public function update($id, $data) {
        return $this->gebruikerDao->update($id, $data);
    }

    // Verwijder gebruiker
    public function delete($id) {
        return $this->gebruikerDao->delete($id);
    }
}
