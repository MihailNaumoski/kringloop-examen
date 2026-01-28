<?php
// GebruikerDao - beheer van gebruikers
class GebruikerDao extends BaseDao {
    protected $table = 'gebruiker';

    // Haal alle gebruikers op als objecten
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table);
        $stmt->execute();
        $items = [];
        foreach($stmt->fetchAll() as $row) {
            $items[] = $this->createGebruikerObject($row);
        }
        return $items;
    }

    // Haal gebruiker op via ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if($row) {
            return $this->createGebruikerObject($row);
        }
        return null;
    }

    // Vind gebruiker op gebruikersnaam
    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE gebruikersnaam = ?");
        $stmt->execute([$username]);
        $row = $stmt->fetch();
        if($row) {
            return $this->createGebruikerObject($row);
        }
        return null;
    }

    // Maak nieuwe gebruiker
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO " . $this->table . " (gebruikersnaam, wachtwoord, rollen, is_geverifieerd) VALUES (?, ?, ?, ?)");
        $hashedPassword = password_hash($data['wachtwoord'], PASSWORD_DEFAULT);
        return $stmt->execute([
            $data['gebruikersnaam'],
            $hashedPassword,
            $data['rollen'],
            $data['is_geverifieerd'] ?? 1
        ]);
    }

    // Update gebruiker
    public function update($id, $data) {
        // Als wachtwoord meegegeven, hash het
        if(!empty($data['wachtwoord'])) {
            $stmt = $this->db->prepare("UPDATE " . $this->table . " SET gebruikersnaam = ?, wachtwoord = ?, rollen = ?, is_geverifieerd = ? WHERE id = ?");
            $hashedPassword = password_hash($data['wachtwoord'], PASSWORD_DEFAULT);
            return $stmt->execute([
                $data['gebruikersnaam'],
                $hashedPassword,
                $data['rollen'],
                $data['is_geverifieerd'],
                $id
            ]);
        } else {
            $stmt = $this->db->prepare("UPDATE " . $this->table . " SET gebruikersnaam = ?, rollen = ?, is_geverifieerd = ? WHERE id = ?");
            return $stmt->execute([
                $data['gebruikersnaam'],
                $data['rollen'],
                $data['is_geverifieerd'],
                $id
            ]);
        }
    }

    // Verifieer login
    public function verifyLogin($username, $password) {
        $user = $this->findByUsername($username);
        if($user && password_verify($password, $user->getWachtwoord())) {
            if($user->getIsGeverifieerd()) {
                return $user;
            }
        }
        return false;
    }

    // Maak juiste gebruiker object op basis van rol (polymorfisme)
    private function createGebruikerObject($data) {
        $rol = $data['rollen'];

        switch($rol) {
            case 'directie':
                $obj = new Directie();
                break;
            case 'magazijnmedewerker':
                $obj = new Magazijnmedewerker();
                break;
            case 'winkelpersoneel':
                $obj = new Winkelpersoneel();
                break;
            case 'chauffeur':
                $obj = new Chauffeur();
                break;
            default:
                $obj = new Gebruiker();
        }

        $obj->setId($data['id']);
        $obj->setGebruikersnaam($data['gebruikersnaam']);
        $obj->setWachtwoord($data['wachtwoord']);
        $obj->setRollen($data['rollen']);
        $obj->setIsGeverifieerd($data['is_geverifieerd']);

        return $obj;
    }

    // Verwijder gebruiker
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM " . $this->table . " WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
