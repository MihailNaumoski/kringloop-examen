<?php
// Persoonsgegevens beheer pagina
require_once 'config/config.php';

$auth->requireLogin();
$auth->requirePermissie('persoonsgegevens_beheren');

// Dao's
$persoonDao = new PersoonDao($db);

$message = '';
$error = '';

// Verwerk acties
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if($action === 'create') {
        $data = [
            'voornaam' => $_POST['voornaam'],
            'achternaam' => $_POST['achternaam'],
            'adres' => $_POST['adres'],
            'plaats' => $_POST['plaats'],
            'email' => $_POST['email'] ?? '',
            'geboortedatum' => $_POST['geboortedatum'] ?? '',
            'telefoon' => $_POST['telefoon'] ?? ''
        ];
        if($persoonDao->create($data)) {
            $message = 'Persoon succesvol toegevoegd.';
        } else {
            $error = 'Fout bij toevoegen persoon.';
        }
    }

    if($action === 'update') {
        $id = $_POST['id'];
        $data = [
            'voornaam' => $_POST['voornaam'],
            'achternaam' => $_POST['achternaam'],
            'adres' => $_POST['adres'],
            'plaats' => $_POST['plaats'],
            'email' => $_POST['email'] ?? '',
            'geboortedatum' => $_POST['geboortedatum'] ?? '',
            'telefoon' => $_POST['telefoon'] ?? ''
        ];
        if($persoonDao->update($id, $data)) {
            $message = 'Persoon succesvol bijgewerkt.';
        } else {
            $error = 'Fout bij bijwerken persoon.';
        }
    }

    if($action === 'delete') {
        $id = $_POST['id'];
        if($persoonDao->delete($id)) {
            $message = 'Persoon succesvol verwijderd.';
        } else {
            $error = 'Fout bij verwijderen persoon.';
        }
    }
}

// Zoeken
$zoekterm = $_GET['zoek'] ?? '';
if($zoekterm) {
    $personen = $persoonDao->zoek($zoekterm);
} else {
    $personen = $persoonDao->getAll();
}

// Edit mode
$editPersoon = null;
if(isset($_GET['edit'])) {
    $editPersoon = $persoonDao->getById($_GET['edit']);
}
$pageTitle = 'Persoonsgegevens';
?>
<?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-person-lines-fill"></i> Persoonsgegevens</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#persoonModal">
                <i class="bi bi-plus-lg"></i> Nieuwe Persoon
            </button>
        </div>

        <?php include 'includes/alerts.php'; ?>

        <!-- Zoekbalk -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-10">
                        <input type="text" name="zoek" class="form-control"
                               placeholder="Zoek op naam..." value="<?= e($zoekterm) ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="bi bi-search"></i> Zoeken
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Personen tabel -->
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Naam</th>
                            <th>Adres</th>
                            <th>Telefoon</th>
                            <th>Email</th>
                            <th>Geboortedatum</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($personen)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">Geen personen gevonden.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach($personen as $persoon): ?>
                        <tr>
                            <td><?= e($persoon->getId()) ?></td>
                            <td><?= e($persoon->getVolledigeNaam()) ?></td>
                            <td><?= e($persoon->getAdres()) ?>, <?= e($persoon->getPlaats()) ?></td>
                            <td><?= e($persoon->getTelefoon() ?: '-') ?></td>
                            <td><?= e($persoon->getEmail() ?: '-') ?></td>
                            <td><?= $persoon->getGeboortedatum() ? formatDatum($persoon->getGeboortedatum()) : '-' ?></td>
                            <td>
                                <a href="?edit=<?= $persoon->getId() ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Weet je het zeker?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $persoon->getId() ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal voor toevoegen/bewerken -->
    <div class="modal fade" id="persoonModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <?= $editPersoon ? 'Persoon Bewerken' : 'Nieuwe Persoon' ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?= $editPersoon ? 'update' : 'create' ?>">
                        <?php if($editPersoon): ?>
                        <input type="hidden" name="id" value="<?= $editPersoon->getId() ?>">
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="voornaam" class="form-label">Voornaam</label>
                                    <input type="text" class="form-control" id="voornaam" name="voornaam" required
                                           value="<?= $editPersoon ? e($editPersoon->getVoornaam()) : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="achternaam" class="form-label">Achternaam</label>
                                    <input type="text" class="form-control" id="achternaam" name="achternaam" required
                                           value="<?= $editPersoon ? e($editPersoon->getAchternaam()) : '' ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="adres" class="form-label">Adres</label>
                            <input type="text" class="form-control" id="adres" name="adres" required
                                   value="<?= $editPersoon ? e($editPersoon->getAdres()) : '' ?>">
                        </div>

                        <div class="mb-3">
                            <label for="plaats" class="form-label">Plaats</label>
                            <input type="text" class="form-control" id="plaats" name="plaats" required
                                   value="<?= $editPersoon ? e($editPersoon->getPlaats()) : '' ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefoon" class="form-label">Telefoon</label>
                                    <input type="tel" class="form-control" id="telefoon" name="telefoon"
                                           value="<?= $editPersoon ? e($editPersoon->getTelefoon()) : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="<?= $editPersoon ? e($editPersoon->getEmail()) : '' ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="geboortedatum" class="form-label">Geboortedatum</label>
                            <input type="date" class="form-control" id="geboortedatum" name="geboortedatum"
                                   value="<?= $editPersoon && $editPersoon->getGeboortedatum() ? $editPersoon->getGeboortedatum() : '' ?>">
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
<?php if($editPersoon): ?>
    <script>new bootstrap.Modal(document.getElementById('persoonModal')).show();</script>
<?php endif; ?>
</body>
</html>
