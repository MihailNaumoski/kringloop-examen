<?php
// WagenDao - beheer van voertuigen
class WagenDao extends BaseDao {
    protected $table = 'wagen';
    protected $entityClass = 'Wagen';

    // Haal alle wagens op als objecten
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " ORDER BY kenteken");
        $stmt->execute();
        $items = [];
        foreach($stmt->fetchAll() as $row) {
            $items[] = Wagen::fromArray($row);
        }
        return $items;
    }

    // Haal wagen op via kenteken
    public function getByKenteken($kenteken) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE kenteken = ?");
        $stmt->execute([strtoupper($kenteken)]);
        $row = $stmt->fetch();
        if($row) {
            return Wagen::fromArray($row);
        }
        return null;
    }

    // Maak nieuwe wagen
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO " . $this->table . " (kenteken, omschrijving) VALUES (?, ?)");
        return $stmt->execute([
            strtoupper($data['kenteken']),
            $data['omschrijving'] ?? null
        ]);
    }

    // Update wagen
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET kenteken = ?, omschrijving = ? WHERE id = ?");
        return $stmt->execute([
            strtoupper($data['kenteken']),
            $data['omschrijving'] ?? null,
            $id
        ]);
    }

}
