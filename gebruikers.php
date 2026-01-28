<?php
// Gebruikers beheer pagina - alleen voor directie
require_once 'config/config.php';

$auth->requireLogin();
$auth->requirePermissie('gebruikers_beheren');

// Dao's
$gebruikerDao = new GebruikerDao($db);

$message = '';
$error = '';

// Verwerk acties
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if($action === 'create') {
        $data = [
            'gebruikersnaam' => $_POST['gebruikersnaam'],
            'wachtwoord' => $_POST['wachtwoord'],
            'rollen' => $_POST['rollen'],
            'is_geverifieerd' => isset($_POST['is_geverifieerd']) ? 1 : 0
        ];
        if($gebruikerDao->create($data)) {
            $message = 'Gebruiker succesvol toegevoegd.';
        } else {
            $error = 'Fout bij toevoegen gebruiker.';
        }
    }

    if($action === 'update') {
        $id = $_POST['id'];
        $data = [
            'gebruikersnaam' => $_POST['gebruikersnaam'],
            'wachtwoord' => $_POST['wachtwoord'] ?? '',
            'rollen' => $_POST['rollen'],
            'is_geverifieerd' => isset($_POST['is_geverifieerd']) ? 1 : 0
        ];
        if($gebruikerDao->update($id, $data)) {
            $message = 'Gebruiker succesvol bijgewerkt.';
        } else {
            $error = 'Fout bij bijwerken gebruiker.';
        }
    }

    if($action === 'delete') {
        $id = $_POST['id'];
        // Voorkom verwijderen eigen account
        if($id == $_SESSION['user_id']) {
            $error = 'Je kunt je eigen account niet verwijderen.';
        } else {
            if($gebruikerDao->delete($id)) {
                $message = 'Gebruiker succesvol verwijderd.';
            } else {
                $error = 'Fout bij verwijderen gebruiker.';
            }
        }
    }

    if($action === 'toggle_verify') {
        $id = $_POST['id'];
        $user = $gebruikerDao->getById($id);
        if($user) {
            $data = [
                'gebruikersnaam' => $user->getGebruikersnaam(),
                'rollen' => $user->getRollen(),
                'is_geverifieerd' => $user->getIsGeverifieerd() ? 0 : 1
            ];
            if($gebruikerDao->update($id, $data)) {
                $message = 'Gebruiker status bijgewerkt.';
            } else {
                $error = 'Fout bij bijwerken status.';
            }
        }
    }
}

// Data ophalen
$gebruikers = $gebruikerDao->getAll();

// Beschikbare rollen
$rollen = ['directie', 'magazijnmedewerker', 'winkelpersoneel', 'chauffeur'];

// Edit mode
$editGebruiker = null;
if(isset($_GET['edit'])) {
    $editGebruiker = $gebruikerDao->getById($_GET['edit']);
}
$pageTitle = 'Gebruikers';
?>
<?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-person-gear"></i> Gebruikers Beheer</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#gebruikerModal">
                <i class="bi bi-plus-lg"></i> Nieuwe Gebruiker
            </button>
        </div>

        <?php include 'includes/alerts.php'; ?>

        <!-- Gebruikers tabel -->
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Gebruikersnaam</th>
                            <th>Rol</th>
                            <th>Status</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($gebruikers as $gebruiker): ?>
                        <tr>
                            <td><?= e($gebruiker->getId()) ?></td>
                            <td>
                                <?= e($gebruiker->getGebruikersnaam()) ?>
                                <?php if($gebruiker->getId() == $_SESSION['user_id']): ?>
                                <span class="badge bg-info">Jij</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= e($gebruiker->getRolNaam()) ?></span>
                            </td>
                            <td>
                                <?php if($gebruiker->getIsGeverifieerd()): ?>
                                <span class="badge bg-success"><i class="bi bi-check"></i> Actief</span>
                                <?php else: ?>
                                <span class="badge bg-danger"><i class="bi bi-x"></i> Geblokkeerd</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <!-- Toggle actief/geblokkeerd -->
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="toggle_verify">
                                    <input type="hidden" name="id" value="<?= $gebruiker->getId() ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-<?= $gebruiker->getIsGeverifieerd() ? 'warning' : 'success' ?>"
                                            title="<?= $gebruiker->getIsGeverifieerd() ? 'Blokkeren' : 'Activeren' ?>">
                                        <i class="bi bi-<?= $gebruiker->getIsGeverifieerd() ? 'lock' : 'unlock' ?>"></i>
                                    </button>
                                </form>

                                <a href="?edit=<?= $gebruiker->getId() ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <?php if($gebruiker->getId() != $_SESSION['user_id']): ?>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Weet je het zeker?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $gebruiker->getId() ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="gebruikerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <?= $editGebruiker ? 'Gebruiker Bewerken' : 'Nieuwe Gebruiker' ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?= $editGebruiker ? 'update' : 'create' ?>">
                        <?php if($editGebruiker): ?>
                        <input type="hidden" name="id" value="<?= $editGebruiker->getId() ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="gebruikersnaam" class="form-label">Gebruikersnaam</label>
                            <input type="text" class="form-control" id="gebruikersnaam" name="gebruikersnaam" required
                                   value="<?= $editGebruiker ? e($editGebruiker->getGebruikersnaam()) : '' ?>">
                        </div>

                        <div class="mb-3">
                            <label for="wachtwoord" class="form-label">
                                Wachtwoord <?= $editGebruiker ? '(laat leeg om niet te wijzigen)' : '' ?>
                            </label>
                            <input type="password" class="form-control" id="wachtwoord" name="wachtwoord"
                                   <?= $editGebruiker ? '' : 'required' ?>>
                        </div>

                        <div class="mb-3">
                            <label for="rollen" class="form-label">Rol</label>
                            <select class="form-select" id="rollen" name="rollen" required>
                                <option value="">Selecteer rol...</option>
                                <?php foreach($rollen as $rol): ?>
                                <option value="<?= $rol ?>"
                                    <?= ($editGebruiker && $editGebruiker->getRollen() === $rol) ? 'selected' : '' ?>>
                                    <?= ucfirst($rol) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_geverifieerd" name="is_geverifieerd"
                                   <?= (!$editGebruiker || $editGebruiker->getIsGeverifieerd()) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_geverifieerd">Account actief</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Opslaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
<?php if($editGebruiker): ?>
    <script>new bootstrap.Modal(document.getElementById('gebruikerModal')).show();</script>
<?php endif; ?>
</body>
</html>
