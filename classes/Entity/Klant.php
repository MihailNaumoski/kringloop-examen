<?php
// Klant class - klanten en donateurs
class Klant {
    private $id;
    private $naam;
    private $adres;
    private $plaats;
    private $telefoon;
    private $email;

    // Constructor
    public function __construct($naam = '', $adres = '', $plaats = '', $telefoon = '', $email = '') {
        $this->naam = $naam;
        $this->adres = $adres;
        $this->plaats = $plaats;
        $this->telefoon = $telefoon;
        $this->email = $email;
    }

    // Maak object van database array
    public static function fromArray($data) {
        $obj = new self();
        $obj->setId($data['id']);
        $obj->setNaam($data['naam']);
        $obj->setAdres($data['adres']);
        $obj->setPlaats($data['plaats']);
        $obj->setTelefoon($data['telefoon']);
        $obj->setEmail($data['email']);
        return $obj;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getNaam() {
        return $this->naam;
    }

    public function getAdres() {
        return $this->adres;
    }

    public function getPlaats() {
        return $this->plaats;
    }

    public function getTelefoon() {
        return $this->telefoon;
    }

    public function getEmail() {
        return $this->email;
    }

    // Volledig adres
    public function getVolledigAdres() {
        return $this->adres . ', ' . $this->plaats;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setNaam($naam) {
        $this->naam = $naam;
    }

    public function setAdres($adres) {
        $this->adres = $adres;
    }

    public function setPlaats($plaats) {
        $this->plaats = $plaats;
    }

    public function setTelefoon($telefoon) {
        $this->telefoon = $telefoon;
    }

    public function setEmail($email) {
        $this->email = $email;
    }
}
