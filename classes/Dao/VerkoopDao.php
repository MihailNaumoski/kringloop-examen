<?php
// VerkoopDao - beheer van verkopen
class VerkoopDao extends BaseDao {
    protected $table = 'verkopen';
    protected $entityClass = 'Verkoop';

    // Haal alle verkopen op als objecten
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " ORDER BY verkocht_op DESC");
        $stmt->execute();
        $items = [];
        foreach($stmt->fetchAll() as $row) {
            $items[] = Verkoop::fromArray($row);
        }
        return $items;
    }

    // Haal verkopen met klant en artikel info
    public function getAllWithDetails() {
        $sql = "SELECT v.*, k.naam as klant_naam, a.naam as artikel_naam, a.prijs_ex_btw
                FROM " . $this->table . " v
                LEFT JOIN klant k ON v.klant_id = k.id
                LEFT JOIN artikel a ON v.artikel_id = a.id
                ORDER BY v.verkocht_op DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Haal verkopen per klant
    public function getByKlant($klantId) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE klant_id = ? ORDER BY verkocht_op DESC");
        $stmt->execute([$klantId]);
        $items = [];
        foreach($stmt->fetchAll() as $row) {
            $items[] = Verkoop::fromArray($row);
        }
        return $items;
    }

    // Haal verkopen per periode
    public function getByPeriode($startDatum, $eindDatum) {
        $sql = "SELECT v.*, k.naam as klant_naam, a.naam as artikel_naam, a.prijs_ex_btw
                FROM " . $this->table . " v
                LEFT JOIN klant k ON v.klant_id = k.id
                LEFT JOIN artikel a ON v.artikel_id = a.id
                WHERE DATE(v.verkocht_op) BETWEEN ? AND ?
                ORDER BY v.verkocht_op DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDatum, $eindDatum]);
        return $stmt->fetchAll();
    }

    // Haal maandoverzicht
    public function getMaandOverzicht($jaar, $maand) {
        $sql = "SELECT v.*, k.naam as klant_naam, a.naam as artikel_naam, a.prijs_ex_btw, c.categorie as categorie_naam
                FROM " . $this->table . " v
                LEFT JOIN klant k ON v.klant_id = k.id
                LEFT JOIN artikel a ON v.artikel_id = a.id
                LEFT JOIN categorie c ON a.categorie_id = c.id
                WHERE YEAR(v.verkocht_op) = ? AND MONTH(v.verkocht_op) = ?
                ORDER BY v.verkocht_op DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$jaar, $maand]);
        return $stmt->fetchAll();
    }

    // Haal omzet per maand
    public function getOmzetPerMaand($jaar, $maand) {
        $sql = "SELECT SUM(a.prijs_ex_btw) as totaal
                FROM " . $this->table . " v
                JOIN artikel a ON v.artikel_id = a.id
                WHERE YEAR(v.verkocht_op) = ? AND MONTH(v.verkocht_op) = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$jaar, $maand]);
        $result = $stmt->fetch();
        return $result['totaal'] ?? 0;
    }

    // Haal omzet per categorie per maand
    public function getOmzetPerCategorie($jaar, $maand) {
        $sql = "SELECT c.categorie, SUM(a.prijs_ex_btw) as totaal, COUNT(*) as aantal
                FROM " . $this->table . " v
                JOIN artikel a ON v.artikel_id = a.id
                JOIN categorie c ON a.categorie_id = c.id
                WHERE YEAR(v.verkocht_op) = ? AND MONTH(v.verkocht_op) = ?
                GROUP BY c.id, c.categorie
                ORDER BY totaal DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$jaar, $maand]);
        return $stmt->fetchAll();
    }

    // Maak nieuwe verkoop
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO " . $this->table . " (klant_id, artikel_id, verkocht_op) VALUES (?, ?, NOW())");
        return $stmt->execute([
            $data['klant_id'],
            $data['artikel_id']
        ]);
    }

    // Update verkoop
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET klant_id = ?, artikel_id = ? WHERE id = ?");
        return $stmt->execute([
            $data['klant_id'],
            $data['artikel_id'],
            $id
        ]);
    }

    // Tel verkopen vandaag
    public function countVandaag() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM " . $this->table . " WHERE DATE(verkocht_op) = CURDATE()");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

}
