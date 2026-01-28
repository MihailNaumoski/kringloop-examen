<?php
// Wagen class - voertuigen voor ophalen/bezorgen
class Wagen {
    private $id;
    private $kenteken;
    private $omschrijving;

    // Constructor
    public function __construct($kenteken = '', $omschrijving = '') {
        $this->kenteken = $kenteken;
        $this->omschrijving = $omschrijving;
    }

    // Maak object van database array
    public static function fromArray($data) {
        $obj = new self();
        $obj->setId($data['id']);
        $obj->setKenteken($data['kenteken']);
        $obj->setOmschrijving($data['omschrijving'] ?? null);
        return $obj;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getKenteken() {
        return $this->kenteken;
    }

    public function getOmschrijving() {
        return $this->omschrijving;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setKenteken($kenteken) {
        $this->kenteken = $kenteken;
    }

    public function setOmschrijving($omschrijving) {
        $this->omschrijving = $omschrijving;
    }
}
