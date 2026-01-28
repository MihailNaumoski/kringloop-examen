<?php
// PersoonDao - beheer van personen die artikelen aanleveren
class PersoonDao extends BaseDao {
    protected $table = 'persoon';
    protected $entityClass = 'Persoon';

    // Haal alle personen op als objecten
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " ORDER BY achternaam, voornaam");
        $stmt->execute();
        $items = [];
        foreach($stmt->fetchAll() as $row) {
            $items[] = Persoon::fromArray($row);
        }
        return $items;
    }

    // Zoek personen op naam
    public function zoek($zoekterm) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE voornaam LIKE ? OR achternaam LIKE ? OR CONCAT(voornaam, ' ', achternaam) LIKE ?");
        $zoek = '%' . $zoekterm . '%';
        $stmt->execute([$zoek, $zoek, $zoek]);
        $items = [];
        foreach($stmt->fetchAll() as $row) {
            $items[] = Persoon::fromArray($row);
        }
        return $items;
    }

    // Maak nieuwe persoon
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO " . $this->table . " (voornaam, achternaam, adres, plaats, email, geboortedatum, telefoon) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['voornaam'],
            $data['achternaam'],
            $data['adres'],
            $data['plaats'],
            $data['email'] ?? null,
            !empty($data['geboortedatum']) ? $data['geboortedatum'] : null,
            $data['telefoon'] ?? null
        ]);
    }

    // Update persoon
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET voornaam = ?, achternaam = ?, adres = ?, plaats = ?, email = ?, geboortedatum = ?, telefoon = ? WHERE id = ?");
        return $stmt->execute([
            $data['voornaam'],
            $data['achternaam'],
            $data['adres'],
            $data['plaats'],
            $data['email'] ?? null,
            !empty($data['geboortedatum']) ? $data['geboortedatum'] : null,
            $data['telefoon'] ?? null,
            $id
        ]);
    }

}
