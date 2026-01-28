<?php
// Persoon class - personen die artikelen aanleveren
class Persoon {
    private $id;
    private $voornaam;
    private $achternaam;
    private $adres;
    private $plaats;
    private $email;
    private $geboortedatum;
    private $telefoon;
    private $datumIngevoerd;

    // Constructor
    public function __construct($voornaam = '', $achternaam = '', $adres = '', $plaats = '') {
        $this->voornaam = $voornaam;
        $this->achternaam = $achternaam;
        $this->adres = $adres;
        $this->plaats = $plaats;
    }

    // Maak object van database array
    public static function fromArray($data) {
        $obj = new self();
        $obj->setId($data['id']);
        $obj->setVoornaam($data['voornaam']);
        $obj->setAchternaam($data['achternaam']);
        $obj->setAdres($data['adres']);
        $obj->setPlaats($data['plaats']);
        $obj->setEmail($data['email'] ?? null);
        $obj->setGeboortedatum($data['geboortedatum'] ?? null);
        $obj->setTelefoon($data['telefoon'] ?? null);
        $obj->setDatumIngevoerd($data['datum_ingevoerd'] ?? null);
        return $obj;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getVoornaam() {
        return $this->voornaam;
    }

    public function getAchternaam() {
        return $this->achternaam;
    }

    public function getVolledigeNaam() {
        return $this->voornaam . ' ' . $this->achternaam;
    }

    public function getAdres() {
        return $this->adres;
    }

    public function getPlaats() {
        return $this->plaats;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getGeboortedatum() {
        return $this->geboortedatum;
    }

    public function getTelefoon() {
        return $this->telefoon;
    }

    public function getDatumIngevoerd() {
        return $this->datumIngevoerd;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setVoornaam($voornaam) {
        $this->voornaam = $voornaam;
    }

    public function setAchternaam($achternaam) {
        $this->achternaam = $achternaam;
    }

    public function setAdres($adres) {
        $this->adres = $adres;
    }

    public function setPlaats($plaats) {
        $this->plaats = $plaats;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setGeboortedatum($geboortedatum) {
        $this->geboortedatum = $geboortedatum;
    }

    public function setTelefoon($telefoon) {
        $this->telefoon = $telefoon;
    }

    public function setDatumIngevoerd($datumIngevoerd) {
        $this->datumIngevoerd = $datumIngevoerd;
    }
}
