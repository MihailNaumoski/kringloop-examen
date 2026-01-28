<?php
// Categorie class - artikel categorieen
class Categorie {
    private $id;
    private $code;
    private $categorie;

    // Constructor
    public function __construct($categorie = '', $code = '') {
        $this->categorie = $categorie;
        $this->code = $code;
    }

    // Maak object van database array
    public static function fromArray($data) {
        $obj = new self();
        $obj->setId($data['id']);
        $obj->setCode($data['code'] ?? null);
        $obj->setCategorie($data['categorie']);
        return $obj;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getCode() {
        return $this->code;
    }

    public function getCategorie() {
        return $this->categorie;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function setCategorie($categorie) {
        $this->categorie = $categorie;
    }
}
