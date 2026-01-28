<?php
// Chauffeur class - extends Gebruiker (overerving)
class Chauffeur extends Gebruiker {

    // Constructor met parent call
    public function __construct($gebruikersnaam = '', $wachtwoord = '', $isGeverifieerd = 0) {
        parent::__construct($gebruikersnaam, $wachtwoord, 'chauffeur', $isGeverifieerd);
    }

    // Polymorfisme: override getRolNaam
    public function getRolNaam() {
        return 'Chauffeur';
    }

    // Polymorfisme: chauffeur permissies
    public function getPermissies() {
        return [
            'planning_beheren'
        ];
    }

    // Chauffeur werkt alleen ma-vr
    public function kanVandaagWerken() {
        $dag = date('N');
        return $dag >= 1 && $dag <= 5; // 1=ma, 5=vr
    }

    // Chauffeur kan eigen planning beheren
    public function kanPlanningBeheren() {
        return true;
    }
}
