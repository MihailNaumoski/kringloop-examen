<?php
// VoorraadDao - beheer van magazijn voorraad
class VoorraadDao extends BaseDao {
    protected $table = 'voorraad';
    protected $entityClass = 'Voorraad';

    // Haal alle voorraad op als objecten
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " ORDER BY ingeboekt_op DESC");
        $stmt->execute();
        $items = [];
        foreach($stmt->fetchAll() as $row) {
            $items[] = Voorraad::fromArray($row);
        }
        return $items;
    }

    // Haal voorraad met artikel en status info
    public function getAllWithDetails() {
        $sql = "SELECT v.*, a.naam as artikel_naam, a.prijs_ex_btw, s.status as status_naam, c.categorie as categorie_naam
                FROM " . $this->table . " v
                LEFT JOIN artikel a ON v.artikel_id = a.id
                LEFT JOIN status s ON v.status_id = s.id
                LEFT JOIN categorie c ON a.categorie_id = c.id
                ORDER BY v.ingeboekt_op DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Haal voorraad per locatie
    public function getByLocatie($locatie) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE locatie = ?");
        $stmt->execute([$locatie]);
        $items = [];
        foreach($stmt->fetchAll() as $row) {
            $items[] = Voorraad::fromArray($row);
        }
        return $items;
    }

    // Haal voorraad per status
    public function getByStatus($statusId) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE status_id = ?");
        $stmt->execute([$statusId]);
        $items = [];
        foreach($stmt->fetchAll() as $row) {
            $items[] = Voorraad::fromArray($row);
        }
        return $items;
    }

    // Maak nieuwe voorraad entry
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO " . $this->table . " (artikel_id, locatie, aantal, status_id, ingeboekt_op) VALUES (?, ?, ?, ?, NOW())");
        return $stmt->execute([
            $data['artikel_id'],
            $data['locatie'],
            $data['aantal'],
            $data['status_id']
        ]);
    }

    // Update voorraad
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET artikel_id = ?, locatie = ?, aantal = ?, status_id = ? WHERE id = ?");
        return $stmt->execute([
            $data['artikel_id'],
            $data['locatie'],
            $data['aantal'],
            $data['status_id'],
            $id
        ]);
    }

    // Update alleen status
    public function updateStatus($id, $statusId) {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET status_id = ? WHERE id = ?");
        return $stmt->execute([$statusId, $id]);
    }

    // Tel totale voorraad
    public function getTotaalAantal() {
        $stmt = $this->db->prepare("SELECT SUM(aantal) as totaal FROM " . $this->table);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['totaal'] ?? 0;
    }

}
