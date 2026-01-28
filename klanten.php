<?php
// Klanten beheer pagina
require_once 'config/config.php';

$auth->requireLogin();
$auth->requirePermissie('klanten_beheren');

// Dao's
$klantDao = new KlantDao($db);

$message = '';
$error = '';

// Verwerk acties
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if($action === 'create') {
        $data = [
            'naam' => $_POST['naam'],
            'adres' => $_POST['adres'],
            'plaats' => $_POST['plaats'],
            'telefoon' => $_POST['telefoon'],
            'email' => $_POST['email']
        ];
        if($klantDao->create($data)) {
            $message = 'Klant succesvol toegevoegd.';
        } else {
            $error = 'Fout bij toevoegen klant.';
        }
    }

    if($action === 'update') {
        $id = $_POST['id'];
        $data = [
            'naam' => $_POST['naam'],
            'adres' => $_POST['adres'],
            'plaats' => $_POST['plaats'],
            'telefoon' => $_POST['telefoon'],
            'email' => $_POST['email']
        ];
        if($klantDao->update($id, $data)) {
            $message = 'Klant succesvol bijgewerkt.';
        } else {
            $error = 'Fout bij bijwerken klant.';
        }
    }

    if($action === 'delete') {
        $id = $_POST['id'];
        if($klantDao->delete($id)) {
            $message = 'Klant succesvol verwijderd.';
        } else {
            $error = 'Fout bij verwijderen klant. Mogelijk gekoppeld aan verkopen.';
        }
    }
}

// Zoeken
$zoekterm = $_GET['zoek'] ?? '';
if($zoekterm) {
    $klanten = $klantDao->zoek($zoekterm);
} else {
    $klanten = $klantDao->getAll();
}

// Edit mode
$editKlant = null;
if(isset($_GET['edit'])) {
    $editKlant = $klantDao->getById($_GET['edit']);
}

// Detail view
$detailKlant = null;
$klantAankopen = [];
if(isset($_GET['detail'])) {
    $detailKlant = $klantDao->getById($_GET['detail']);
    if($detailKlant) {
        $klantAankopen = $klantDao->getAankopen($_GET['detail']);
    }
}
$pageTitle = 'Klanten';
?>
<?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-people"></i> Klanten</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#klantModal">
                <i class="bi bi-plus-lg"></i> Nieuwe Klant
            </button>
        </div>

        <?php include 'includes/alerts.php'; ?>

        <!-- Zoekbalk -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-10">
                        <input type="text" name="zoek" class="form-control"
                               placeholder="Zoek op naam of email..." value="<?= e($zoekterm) ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="bi bi-search"></i> Zoeken
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Klanten tabel -->
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
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($klanten)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Geen klanten gevonden.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach($klanten as $klant): ?>
                        <tr>
                            <td><?= e($klant->getId()) ?></td>
                            <td><?= e($klant->getNaam()) ?></td>
                            <td><?= e($klant->getAdres()) ?>, <?= e($klant->getPlaats()) ?></td>
                            <td><?= e($klant->getTelefoon()) ?></td>
                            <td><?= e($klant->getEmail()) ?></td>
                            <td>
                                <a href="?detail=<?= $klant->getId() ?>" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="?edit=<?= $klant->getId() ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Weet je het zeker?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $klant->getId() ?>">
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
    <div class="modal fade" id="klantModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <?= $editKlant ? 'Klant Bewerken' : 'Nieuwe Klant' ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?= $editKlant ? 'update' : 'create' ?>">
                        <?php if($editKlant): ?>
                        <input type="hidden" name="id" value="<?= $editKlant->getId() ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="naam" class="form-label">Naam</label>
                            <input type="text" class="form-control" id="naam" name="naam" required
                                   value="<?= $editKlant ? e($editKlant->getNaam()) : '' ?>">
                        </div>

                        <div class="mb-3">
                            <label for="adres" class="form-label">Adres</label>
                            <input type="text" class="form-control" id="adres" name="adres" required
                                   value="<?= $editKlant ? e($editKlant->getAdres()) : '' ?>">
                        </div>

                        <div class="mb-3">
                            <label for="plaats" class="form-label">Plaats</label>
                            <input type="text" class="form-control" id="plaats" name="plaats" required
                                   value="<?= $editKlant ? e($editKlant->getPlaats()) : '' ?>">
                        </div>

                        <div class="mb-3">
                            <label for="telefoon" class="form-label">Telefoon</label>
                            <input type="tel" class="form-control" id="telefoon" name="telefoon" required
                                   value="<?= $editKlant ? e($editKlant->getTelefoon()) : '' ?>">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required
                                   value="<?= $editKlant ? e($editKlant->getEmail()) : '' ?>">
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

    <!-- Modal voor klant details -->
    <?php if($detailKlant): ?>
    <div class="modal fade show" id="detailModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-person"></i> <?= e($detailKlant->getNaam()) ?>
                    </h5>
                    <a href="klanten.php" class="btn-close"></a>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Adres:</strong> <?= e($detailKlant->getVolledigAdres()) ?></p>
                            <p><strong>Telefoon:</strong> <?= e($detailKlant->getTelefoon()) ?></p>
                            <p><strong>Email:</strong> <?= e($detailKlant->getEmail()) ?></p>
                        </div>
                    </div>

                    <h6><i class="bi bi-cart"></i> Aankoopgeschiedenis</h6>
                    <?php if(empty($klantAankopen)): ?>
                    <p class="text-muted">Geen aankopen gevonden.</p>
                    <?php else: ?>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Datum</th>
                                <th>Artikel</th>
                                <th class="text-end">Prijs</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($klantAankopen as $aankoop): ?>
                            <tr>
                                <td><?= formatDatumTijd($aankoop['verkocht_op']) ?></td>
                                <td><?= e($aankoop['artikel_naam']) ?></td>
                                <td class="text-end">&euro; <?= formatPrijs($aankoop['prijs_ex_btw']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <a href="klanten.php" class="btn btn-secondary">Sluiten</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

<?php include 'includes/footer.php'; ?>
<?php if($editKlant): ?>
    <script>new bootstrap.Modal(document.getElementById('klantModal')).show();</script>
<?php endif; ?>
</body>
</html>
