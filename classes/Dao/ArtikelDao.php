<?php
// ArtikelDao - beheer van artikelen
class ArtikelDao extends BaseDao {
    protected $table = 'artikel';
    protected $entityClass = 'Artikel';

    // Haal alle artikelen op als objecten
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table);
        $stmt->execute();
        $items = [];
        foreach($stmt->fetchAll() as $row) {
            $items[] = Artikel::fromArray($row);
        }
        return $items;
    }

    // Haal artikelen op per categorie
    public function getByCategorie($categorieId) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE categorie_id = ?");
        $stmt->execute([$categorieId]);
        $items = [];
        foreach($stmt->fetchAll() as $row) {
            $items[] = Artikel::fromArray($row);
        }
        return $items;
    }

    // Zoek artikelen op naam
    public function zoek($zoekterm) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE naam LIKE ?");
        $stmt->execute(['%' . $zoekterm . '%']);
        $items = [];
        foreach($stmt->fetchAll() as $row) {
            $items[] = Artikel::fromArray($row);
        }
        return $items;
    }

    // Maak nieuw artikel
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO " . $this->table . " (categorie_id, naam, omschrijving, merk, kleur, afmeting_maat, ean_nummer, prijs_ex_btw) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['categorie_id'],
            $data['naam'],
            $data['omschrijving'] ?? null,
            $data['merk'] ?? null,
            $data['kleur'] ?? null,
            $data['afmeting_maat'] ?? null,
            $data['ean_nummer'] ?? null,
            $data['prijs_ex_btw']
        ]);
    }

    // Update artikel
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE " . $this->table . " SET categorie_id = ?, naam = ?, omschrijving = ?, merk = ?, kleur = ?, afmeting_maat = ?, ean_nummer = ?, prijs_ex_btw = ? WHERE id = ?");
        return $stmt->execute([
            $data['categorie_id'],
            $data['naam'],
            $data['omschrijving'] ?? null,
            $data['merk'] ?? null,
            $data['kleur'] ?? null,
            $data['afmeting_maat'] ?? null,
            $data['ean_nummer'] ?? null,
            $data['prijs_ex_btw'],
            $id
        ]);
    }

    // Haal artikelen met categorie naam
    public function getAllWithCategorie() {
        $sql = "SELECT a.*, c.categorie as categorie_naam
                FROM " . $this->table . " a
                LEFT JOIN categorie c ON a.categorie_id = c.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

}
