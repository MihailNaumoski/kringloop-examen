<?php
// BaseDao - abstract parent class voor alle DAO's
abstract class BaseDao {
    protected $db;
    protected $table;
    protected $entityClass = null;

    // Constructor met database connectie
    public function __construct($db) {
        $this->db = $db;
    }

    // Haal alle records op
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Haal record op via ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if($row && $this->entityClass) {
            $class = $this->entityClass;
            return $class::fromArray($row);
        }
        return $row ?: null;
    }

    // Verwijder record via ID
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Tel totaal aantal records
    public function count() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM " . $this->table);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Abstract methodes voor child classes
    abstract public function create($data);
    abstract public function update($id, $data);
}
