<?php
// Verkoop class - verkoop transacties
class Verkoop {
    private $id;
    private $klantId;
    private $artikelId;
    private $verkochtOp;

    // Constructor
    public function __construct($klantId = 0, $artikelId = 0, $verkochtOp = '') {
        $this->klantId = $klantId;
        $this->artikelId = $artikelId;
        $this->verkochtOp = $verkochtOp ?: date('Y-m-d H:i:s');
    }

    // Maak object van database array
    public static function fromArray($data) {
        $obj = new self();
        $obj->setId($data['id']);
        $obj->setKlantId($data['klant_id']);
        $obj->setArtikelId($data['artikel_id']);
        $obj->setVerkochtOp($data['verkocht_op']);
        return $obj;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getKlantId() {
        return $this->klantId;
    }

    public function getArtikelId() {
        return $this->artikelId;
    }

    public function getVerkochtOp() {
        return $this->verkochtOp;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setKlantId($klantId) {
        $this->klantId = $klantId;
    }

    public function setArtikelId($artikelId) {
        $this->artikelId = $artikelId;
    }

    public function setVerkochtOp($verkochtOp) {
        $this->verkochtOp = $verkochtOp;
    }
}
