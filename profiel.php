<?php
// Profiel pagina - wachtwoord wijzigen
require_once 'config/config.php';

$auth->requireLogin();

$message = '';
$error = '';

// Huidige gebruiker
$gebruiker = $auth->getCurrentUser();

// Verwerk wachtwoord wijziging
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oudWachtwoord = $_POST['oud_wachtwoord'] ?? '';
    $nieuwWachtwoord = $_POST['nieuw_wachtwoord'] ?? '';
    $bevestigWachtwoord = $_POST['bevestig_wachtwoord'] ?? '';

    if(empty($oudWachtwoord) || empty($nieuwWachtwoord) || empty($bevestigWachtwoord)) {
        $error = 'Vul alle velden in.';
    } elseif($nieuwWachtwoord !== $bevestigWachtwoord) {
        $error = 'De nieuwe wachtwoorden komen niet overeen.';
    } elseif(strlen($nieuwWachtwoord) < 6) {
        $error = 'Het nieuwe wachtwoord moet minimaal 6 tekens bevatten.';
    } else {
        if($auth->wijzigWachtwoord($_SESSION['user_id'], $oudWachtwoord, $nieuwWachtwoord)) {
            $message = 'Wachtwoord succesvol gewijzigd.';
        } else {
            $error = 'Het huidige wachtwoord is onjuist.';
        }
    }
}
$pageTitle = 'Profiel';
?>
<?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header bg-white">
                        <h4 class="mb-0"><i class="bi bi-person-circle"></i> Mijn Profiel</h4>
                    </div>
                    <div class="card-body">
                        <?php if($message): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> <?= e($message) ?>
                        </div>
                        <?php endif; ?>

                        <?php if($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> <?= e($error) ?>
                        </div>
                        <?php endif; ?>

                        <!-- Gebruiker info -->
                        <div class="mb-4">
                            <div class="row mb-2">
                                <div class="col-4 text-muted">Gebruikersnaam:</div>
                                <div class="col-8"><strong><?= e($gebruiker->getGebruikersnaam()) ?></strong></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4 text-muted">Rol:</div>
                                <div class="col-8"><span class="badge bg-secondary"><?= e($gebruiker->getRolNaam()) ?></span></div>
                            </div>
                            <div class="row">
                                <div class="col-4 text-muted">Status:</div>
                                <div class="col-8">
                                    <?php if($gebruiker->getIsGeverifieerd()): ?>
                                    <span class="badge bg-success">Actief</span>
                                    <?php else: ?>
                                    <span class="badge bg-danger">Inactief</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Wachtwoord wijzigen -->
                        <h5 class="mb-3"><i class="bi bi-key"></i> Wachtwoord Wijzigen</h5>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="oud_wachtwoord" class="form-label">Huidig wachtwoord</label>
                                <input type="password" class="form-control" id="oud_wachtwoord" name="oud_wachtwoord" required>
                            </div>

                            <div class="mb-3">
                                <label for="nieuw_wachtwoord" class="form-label">Nieuw wachtwoord</label>
                                <input type="password" class="form-control" id="nieuw_wachtwoord" name="nieuw_wachtwoord" required minlength="6">
                                <small class="text-muted">Minimaal 6 tekens</small>
                            </div>

                            <div class="mb-3">
                                <label for="bevestig_wachtwoord" class="form-label">Bevestig nieuw wachtwoord</label>
                                <input type="password" class="form-control" id="bevestig_wachtwoord" name="bevestig_wachtwoord" required>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Wachtwoord Wijzigen
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
