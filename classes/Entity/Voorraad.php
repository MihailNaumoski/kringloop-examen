<?php
// Voorraad class - magazijn voorraad beheer
class Voorraad {
    private $id;
    private $artikelId;
    private $locatie;
    private $aantal;
    private $statusId;
    private $ingeboektOp;

    // Constructor
    public function __construct($artikelId = 0, $locatie = '', $aantal = 0, $statusId = 0) {
        $this->artikelId = $artikelId;
        $this->locatie = $locatie;
        $this->aantal = $aantal;
        $this->statusId = $statusId;
        $this->ingeboektOp = date('Y-m-d H:i:s');
    }

    // Maak object van database array
    public static function fromArray($data) {
        $obj = new self();
        $obj->setId($data['id']);
        $obj->setArtikelId($data['artikel_id']);
        $obj->setLocatie($data['locatie']);
        $obj->setAantal($data['aantal']);
        $obj->setStatusId($data['status_id']);
        $obj->setIngeboektOp($data['ingeboekt_op']);
        return $obj;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getArtikelId() {
        return $this->artikelId;
    }

    public function getLocatie() {
        return $this->locatie;
    }

    public function getAantal() {
        return $this->aantal;
    }

    public function getStatusId() {
        return $this->statusId;
    }

    public function getIngeboektOp() {
        return $this->ingeboektOp;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setArtikelId($artikelId) {
        $this->artikelId = $artikelId;
    }

    public function setLocatie($locatie) {
        $this->locatie = $locatie;
    }

    public function setAantal($aantal) {
        $this->aantal = $aantal;
    }

    public function setStatusId($statusId) {
        $this->statusId = $statusId;
    }

    public function setIngeboektOp($ingeboektOp) {
        $this->ingeboektOp = $ingeboektOp;
    }
}
