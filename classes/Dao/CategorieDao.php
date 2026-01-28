<?php
// CategorieDao - beheer van categorieen
class CategorieDao extends BaseDao {
    protected $table = 'categorie';
    protected $entityClass = 'Categorie';

    // Haal alle categorieen op als objecten
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " ORDER BY categorie");
        $stmt->execute();
        $items = [];
        foreach($stmt->fetchAll() as $row) {
            $items[] = Categorie::fromArray($row);
        }
        return $items;
    }

    // Maak nieuwe categorie
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO " . $this->table . " (code, categorie) VALUES (?, ?)");
        return $stmt->execute([
            $data['code'] ?? null,
            $data['categorie']
        ]);
    }

    // Update categorie
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET code = ?, categorie = ? WHERE id = ?");
        return $stmt->execute([
            $data['code'] ?? null,
            $data['categorie'],
            $id
        ]);
    }

    // Tel artikelen per categorie
    public function countArtikelen($categorieId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM artikel WHERE categorie_id = ?");
        $stmt->execute([$categorieId]);
        $result = $stmt->fetch();
        return $result['total'];
    }

}
