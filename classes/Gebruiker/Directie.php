<?php
// Directie class - extends Gebruiker (overerving)
class Directie extends Gebruiker {

    // Constructor met parent call
    public function __construct($gebruikersnaam = '', $wachtwoord = '', $isGeverifieerd = 0) {
        parent::__construct($gebruikersnaam, $wachtwoord, 'directie', $isGeverifieerd);
    }

    // Polymorfisme: override getRolNaam
    public function getRolNaam() {
        return 'Directie';
    }

    // Polymorfisme: directie heeft alle permissies
    public function getPermissies() {
        return [
            'persoonsgegevens_beheren',
            'klanten_beheren',
            'voorraad_beheren',
            'artikelen_beheren',
            'winkelvoorraad_beheren',
            'verkopen_bekijken',
            'verkopen_registreren',
            'planning_beheren',
            'maandoverzicht_bekijken',
            'gebruikers_beheren',
            'categorieen_beheren'
        ];
    }

    // Directie kan gebruikers toevoegen
    public function kanGebruikersToevoegen() {
        return true;
    }

    // Directie kan gebruikers blokkeren
    public function kanGebruikersBlokkeren() {
        return true;
    }
}
