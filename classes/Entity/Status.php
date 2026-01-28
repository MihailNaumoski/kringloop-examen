<?php
// Status class - artikel status (in reparatie, verkoopklaar, etc.)
class Status {
    private $id;
    private $status;

    // Constructor
    public function __construct($status = '') {
        $this->status = $status;
    }

    // Maak object van database array
    public static function fromArray($data) {
        $obj = new self();
        $obj->setId($data['id']);
        $obj->setStatus($data['status']);
        return $obj;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getStatus() {
        return $this->status;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setStatus($status) {
        $this->status = $status;
    }
}
