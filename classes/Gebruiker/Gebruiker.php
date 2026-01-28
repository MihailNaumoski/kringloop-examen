<?php
// Gebruiker parent class - demonstreert encapsulatie
class Gebruiker {
    // Private attributen voor encapsulatie
    private $id;
    private $gebruikersnaam;
    private $wachtwoord;
    private $rollen;
    private $isGeverifieerd;

    // Constructor
    public function __construct($gebruikersnaam = '', $wachtwoord = '', $rollen = '', $isGeverifieerd = 0) {
        $this->gebruikersnaam = $gebruikersnaam;
        $this->wachtwoord = $wachtwoord;
        $this->rollen = $rollen;
        $this->isGeverifieerd = $isGeverifieerd;
    }

    // Maak object van database array
    public static function fromArray($data) {
        $obj = new self();
        $obj->setId($data['id']);
        $obj->setGebruikersnaam($data['gebruikersnaam']);
        $obj->setWachtwoord($data['wachtwoord']);
        $obj->setRollen($data['rollen']);
        $obj->setIsGeverifieerd($data['is_geverifieerd']);
        return $obj;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getGebruikersnaam() {
        return $this->gebruikersnaam;
    }

    public function getWachtwoord() {
        return $this->wachtwoord;
    }

    public function getRollen() {
        return $this->rollen;
    }

    public function getIsGeverifieerd() {
        return $this->isGeverifieerd;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setGebruikersnaam($gebruikersnaam) {
        $this->gebruikersnaam = $gebruikersnaam;
    }

    public function setWachtwoord($wachtwoord) {
        $this->wachtwoord = $wachtwoord;
    }

    public function setRollen($rollen) {
        $this->rollen = $rollen;
    }

    public function setIsGeverifieerd($isGeverifieerd) {
        $this->isGeverifieerd = $isGeverifieerd;
    }

    // Polymorfisme: override in child classes
    public function getRolNaam() {
        return $this->rollen;
    }

    // Controleer of gebruiker bepaalde rol heeft
    public function heeftRol($rol) {
        return strpos($this->rollen, $rol) !== false;
    }

    // Polymorfisme: permissies per rol
    public function getPermissies() {
        return [];
    }
}
