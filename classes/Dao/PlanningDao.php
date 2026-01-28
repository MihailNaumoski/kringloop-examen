<?php
// PlanningDao - beheer van ritten planning
class PlanningDao extends BaseDao {
    protected $table = 'planning';
    protected $entityClass = 'Planning';

    // Haal alle planning op als objecten
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " ORDER BY afspraak_op");
        $stmt->execute();
        $items = [];
        foreach($stmt->fetchAll() as $row) {
            $items[] = Planning::fromArray($row);
        }
        return $items;
    }

    // Haal planning met klant en artikel info
    public function getAllWithDetails() {
        $sql = "SELECT p.*, k.naam as klant_naam, k.adres, k.plaats, a.naam as artikel_naam
                FROM " . $this->table . " p
                LEFT JOIN klant k ON p.klant_id = k.id
                LEFT JOIN artikel a ON p.artikel_id = a.id
                ORDER BY p.afspraak_op";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Haal planning per datum
    public function getByDatum($datum) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE DATE(afspraak_op) = ?");
        $stmt->execute([$datum]);
        $items = [];
        foreach($stmt->fetchAll() as $row) {
            $items[] = Planning::fromArray($row);
        }
        return $items;
    }

    // Haal planning per kenteken (voertuig)
    public function getByKenteken($kenteken) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE kenteken = ? ORDER BY afspraak_op");
        $stmt->execute([$kenteken]);
        $items = [];
        foreach($stmt->fetchAll() as $row) {
            $items[] = Planning::fromArray($row);
        }
        return $items;
    }

    // Haal ophaal ritten
    public function getOphalen() {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE ophalen_of_bezorgen = 'ophalen' ORDER BY afspraak_op");
        $stmt->execute();
        $items = [];
        foreach($stmt->fetchAll() as $row) {
            $items[] = Planning::fromArray($row);
        }
        return $items;
    }

    // Haal bezorg ritten
    public function getBezorgen() {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE ophalen_of_bezorgen = 'bezorgen' ORDER BY afspraak_op");
        $stmt->execute();
        $items = [];
        foreach($stmt->fetchAll() as $row) {
            $items[] = Planning::fromArray($row);
        }
        return $items;
    }

    // Maak nieuwe planning
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO " . $this->table . " (artikel_id, klant_id, kenteken, ophalen_of_bezorgen, afspraak_op) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['artikel_id'],
            $data['klant_id'],
            $data['kenteken'],
            $data['ophalen_of_bezorgen'],
            $data['afspraak_op']
        ]);
    }

    // Update planning
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET artikel_id = ?, klant_id = ?, kenteken = ?, ophalen_of_bezorgen = ?, afspraak_op = ? WHERE id = ?");
        return $stmt->execute([
            $data['artikel_id'],
            $data['klant_id'],
            $data['kenteken'],
            $data['ophalen_of_bezorgen'],
            $data['afspraak_op'],
            $id
        ]);
    }

    // Haal unieke kentekens (voertuigen)
    public function getKentekens() {
        $stmt = $this->db->prepare("SELECT DISTINCT kenteken FROM " . $this->table);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

}
