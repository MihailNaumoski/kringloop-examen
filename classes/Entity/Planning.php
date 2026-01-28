<?php
// Planning class - ritten planning voor ophalen/bezorgen
class Planning {
    private $id;
    private $artikelId;
    private $klantId;
    private $kenteken;
    private $ophalenOfBezorgen;
    private $afspraakOp;

    // Constructor
    public function __construct($artikelId = 0, $klantId = 0, $kenteken = '', $ophalenOfBezorgen = 'ophalen', $afspraakOp = '') {
        $this->artikelId = $artikelId;
        $this->klantId = $klantId;
        $this->kenteken = $kenteken;
        $this->ophalenOfBezorgen = $ophalenOfBezorgen;
        $this->afspraakOp = $afspraakOp ?: date('Y-m-d H:i:s');
    }

    // Maak object van database array
    public static function fromArray($data) {
        $obj = new self();
        $obj->setId($data['id']);
        $obj->setArtikelId($data['artikel_id']);
        $obj->setKlantId($data['klant_id']);
        $obj->setKenteken($data['kenteken']);
        $obj->setOphalenOfBezorgen($data['ophalen_of_bezorgen']);
        $obj->setAfspraakOp($data['afspraak_op']);
        return $obj;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getArtikelId() {
        return $this->artikelId;
    }

    public function getKlantId() {
        return $this->klantId;
    }

    public function getKenteken() {
        return $this->kenteken;
    }

    public function getOphalenOfBezorgen() {
        return $this->ophalenOfBezorgen;
    }

    public function getAfspraakOp() {
        return $this->afspraakOp;
    }

    // Is dit een ophaal rit?
    public function isOphalen() {
        return $this->ophalenOfBezorgen === 'ophalen';
    }

    // Is dit een bezorg rit?
    public function isBezorgen() {
        return $this->ophalenOfBezorgen === 'bezorgen';
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setArtikelId($artikelId) {
        $this->artikelId = $artikelId;
    }

    public function setKlantId($klantId) {
        $this->klantId = $klantId;
    }

    public function setKenteken($kenteken) {
        $this->kenteken = $kenteken;
    }

    public function setOphalenOfBezorgen($ophalenOfBezorgen) {
        $this->ophalenOfBezorgen = $ophalenOfBezorgen;
    }

    public function setAfspraakOp($afspraakOp) {
        $this->afspraakOp = $afspraakOp;
    }
}
