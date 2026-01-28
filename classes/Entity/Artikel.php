<?php
// Artikel class - product in de kringloopwinkel
class Artikel {
    private $id;
    private $categorieId;
    private $naam;
    private $omschrijving;
    private $merk;
    private $kleur;
    private $afmetingMaat;
    private $eanNummer;
    private $prijsExBtw;

    // Constructor
    public function __construct($naam = '', $categorieId = 0, $prijsExBtw = 0.00) {
        $this->naam = $naam;
        $this->categorieId = $categorieId;
        $this->prijsExBtw = $prijsExBtw;
    }

    // Maak object van database array
    public static function fromArray($data) {
        $obj = new self();
        $obj->setId($data['id']);
        $obj->setCategorieId($data['categorie_id']);
        $obj->setNaam($data['naam']);
        $obj->setOmschrijving($data['omschrijving'] ?? null);
        $obj->setMerk($data['merk'] ?? null);
        $obj->setKleur($data['kleur'] ?? null);
        $obj->setAfmetingMaat($data['afmeting_maat'] ?? null);
        $obj->setEanNummer($data['ean_nummer'] ?? null);
        $obj->setPrijsExBtw($data['prijs_ex_btw']);
        return $obj;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getCategorieId() {
        return $this->categorieId;
    }

    public function getNaam() {
        return $this->naam;
    }

    public function getOmschrijving() {
        return $this->omschrijving;
    }

    public function getMerk() {
        return $this->merk;
    }

    public function getKleur() {
        return $this->kleur;
    }

    public function getAfmetingMaat() {
        return $this->afmetingMaat;
    }

    public function getEanNummer() {
        return $this->eanNummer;
    }

    public function getPrijsExBtw() {
        return $this->prijsExBtw;
    }

    // Bereken prijs inclusief BTW (21%)
    public function getPrijsInclBtw() {
        return $this->prijsExBtw * 1.21;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setCategorieId($categorieId) {
        $this->categorieId = $categorieId;
    }

    public function setNaam($naam) {
        $this->naam = $naam;
    }

    public function setOmschrijving($omschrijving) {
        $this->omschrijving = $omschrijving;
    }

    public function setMerk($merk) {
        $this->merk = $merk;
    }

    public function setKleur($kleur) {
        $this->kleur = $kleur;
    }

    public function setAfmetingMaat($afmetingMaat) {
        $this->afmetingMaat = $afmetingMaat;
    }

    public function setEanNummer($eanNummer) {
        $this->eanNummer = $eanNummer;
    }

    public function setPrijsExBtw($prijsExBtw) {
        $this->prijsExBtw = $prijsExBtw;
    }
}
