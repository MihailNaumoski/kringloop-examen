<?php
// StatusDao - beheer van artikel statussen
class StatusDao extends BaseDao {
    protected $table = 'status';
    protected $entityClass = 'Status';

    // Haal alle statussen op als objecten
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " ORDER BY status");
        $stmt->execute();
        $items = [];
        foreach($stmt->fetchAll() as $row) {
            $items[] = Status::fromArray($row);
        }
        return $items;
    }

    // Maak nieuwe status
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO " . $this->table . " (status) VALUES (?)");
        return $stmt->execute([
            $data['status']
        ]);
    }

    // Update status
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET status = ? WHERE id = ?");
        return $stmt->execute([
            $data['status'],
            $id
        ]);
    }

}
