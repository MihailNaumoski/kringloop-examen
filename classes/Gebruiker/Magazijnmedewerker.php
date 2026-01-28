<?php
// Magazijnmedewerker class - extends Gebruiker (overerving)
class Magazijnmedewerker extends Gebruiker {

    // Constructor met parent call
    public function __construct($gebruikersnaam = '', $wachtwoord = '', $isGeverifieerd = 0) {
        parent::__construct($gebruikersnaam, $wachtwoord, 'magazijnmedewerker', $isGeverifieerd);
    }

    // Polymorfisme: override getRolNaam
    public function getRolNaam() {
        return 'Magazijnmedewerker';
    }

    // Polymorfisme: magazijn permissies
    public function getPermissies() {
        return [
            'voorraad_beheren',
            'artikelen_beheren'
        ];
    }

    // Magazijnmedewerker werkt alleen met magazijn
    public function kanMagazijnBeheren() {
        return true;
    }
}
