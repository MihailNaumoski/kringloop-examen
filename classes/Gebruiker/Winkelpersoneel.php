<?php
// Winkelpersoneel class - extends Gebruiker (overerving)
class Winkelpersoneel extends Gebruiker {

    // Constructor met parent call
    public function __construct($gebruikersnaam = '', $wachtwoord = '', $isGeverifieerd = 0) {
        parent::__construct($gebruikersnaam, $wachtwoord, 'winkelpersoneel', $isGeverifieerd);
    }

    // Polymorfisme: override getRolNaam
    public function getRolNaam() {
        return 'Winkelpersoneel';
    }

    // Polymorfisme: winkel permissies
    public function getPermissies() {
        return [
            'winkelvoorraad_beheren',
            'klanten_beheren',
            'persoonsgegevens_beheren',
            'planning_beheren'
        ];
    }

    // Winkelpersoneel kan verkopen registreren
    public function kanVerkopenRegistreren() {
        return true;
    }
}
