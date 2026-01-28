<?php
// KlantDao - beheer van klanten
class KlantDao extends BaseDao {
    protected $table = 'klant';
    protected $entityClass = 'Klant';

    // Haal alle klanten op als objecten
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " ORDER BY naam");
        $stmt->execute();
        $items = [];
        foreach($stmt->fetchAll() as $row) {
            $items[] = Klant::fromArray($row);
        }
        return $items;
    }

    // Zoek klanten op naam
    public function zoek($zoekterm) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE naam LIKE ? OR email LIKE ?");
        $stmt->execute(['%' . $zoekterm . '%', '%' . $zoekterm . '%']);
        $items = [];
        foreach($stmt->fetchAll() as $row) {
            $items[] = Klant::fromArray($row);
        }
        return $items;
    }

    // Maak nieuwe klant
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO " . $this->table . " (naam, adres, plaats, telefoon, email) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['naam'],
            $data['adres'],
            $data['plaats'],
            $data['telefoon'],
            $data['email']
        ]);
    }

    // Update klant
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET naam = ?, adres = ?, plaats = ?, telefoon = ?, email = ? WHERE id = ?");
        return $stmt->execute([
            $data['naam'],
            $data['adres'],
            $data['plaats'],
            $data['telefoon'],
            $data['email'],
            $id
        ]);
    }

    // Haal aankoopgeschiedenis van klant
    public function getAankopen($klantId) {
        $sql = "SELECT v.*, a.naam as artikel_naam, a.prijs_ex_btw
                FROM verkopen v
                JOIN artikel a ON v.artikel_id = a.id
                WHERE v.klant_id = ?
                ORDER BY v.verkocht_op DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$klantId]);
        return $stmt->fetchAll();
    }

}
