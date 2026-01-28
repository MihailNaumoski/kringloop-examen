<?php
// Wagens beheer pagina
require_once 'config/config.php';

$auth->requireLogin();
$auth->requirePermissie('planning_beheren');

// Dao's
$wagenDao = new WagenDao($db);

$message = '';
$error = '';

// Verwerk acties
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if($action === 'create') {
        $data = [
            'kenteken' => $_POST['kenteken'],
            'omschrijving' => $_POST['omschrijving'] ?? ''
        ];
        // Check of kenteken al bestaat
        if($wagenDao->getByKenteken($data['kenteken'])) {
            $error = 'Dit kenteken bestaat al.';
        } else {
            if($wagenDao->create($data)) {
                $message = 'Wagen succesvol toegevoegd.';
            } else {
                $error = 'Fout bij toevoegen wagen.';
            }
        }
    }

    if($action === 'update') {
        $id = $_POST['id'];
        $data = [
            'kenteken' => $_POST['kenteken'],
            'omschrijving' => $_POST['omschrijving'] ?? ''
        ];
        // Check of kenteken al bestaat bij andere wagen
        $bestaande = $wagenDao->getByKenteken($data['kenteken']);
        if($bestaande && $bestaande->getId() != $id) {
            $error = 'Dit kenteken is al in gebruik.';
        } else {
            if($wagenDao->update($id, $data)) {
                $message = 'Wagen succesvol bijgewerkt.';
            } else {
                $error = 'Fout bij bijwerken wagen.';
            }
        }
    }

    if($action === 'delete') {
        $id = $_POST['id'];
        if($wagenDao->delete($id)) {
            $message = 'Wagen succesvol verwijderd.';
        } else {
            $error = 'Fout bij verwijderen wagen.';
        }
    }
}

// Data ophalen
$wagens = $wagenDao->getAll();

// Edit mode
$editWagen = null;
if(isset($_GET['edit'])) {
    $editWagen = $wagenDao->getById($_GET['edit']);
}
$pageTitle = 'Wagens';
?>
<?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-truck"></i> Wagens</h2>
            <?php if($editWagen): ?>
            <a href="wagens.php" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Nieuwe Wagen
            </a>
            <?php else: ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#wagenModal">
                <i class="bi bi-plus-lg"></i> Nieuwe Wagen
            </button>
            <?php endif; ?>
        </div>

        <?php include 'includes/alerts.php'; ?>

        <!-- Wagens tabel -->
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kenteken</th>
                            <th>Omschrijving</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($wagens)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">Geen wagens gevonden.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach($wagens as $wagen): ?>
                        <tr>
                            <td><?= e($wagen->getId()) ?></td>
                            <td><code><?= e($wagen->getKenteken()) ?></code></td>
                            <td><?= e($wagen->getOmschrijving() ?: '-') ?></td>
                            <td>
                                <a href="?edit=<?= $wagen->getId() ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Weet je het zeker?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $wagen->getId() ?>">
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
    <div class="modal fade" id="wagenModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <?= $editWagen ? 'Wagen Bewerken' : 'Nieuwe Wagen' ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?= $editWagen ? 'update' : 'create' ?>">
                        <?php if($editWagen): ?>
                        <input type="hidden" name="id" value="<?= $editWagen->getId() ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="kenteken" class="form-label">Kenteken</label>
                            <input type="text" class="form-control" id="kenteken" name="kenteken" required
                                   placeholder="bijv. AB-123-CD" style="text-transform: uppercase;"
                                   value="<?= $editWagen ? e($editWagen->getKenteken()) : '' ?>">
                        </div>

                        <div class="mb-3">
                            <label for="omschrijving" class="form-label">Omschrijving</label>
                            <input type="text" class="form-control" id="omschrijving" name="omschrijving"
                                   placeholder="bijv. Vrachtwagen 1"
                                   value="<?= $editWagen ? e($editWagen->getOmschrijving()) : '' ?>">
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
<?php if($editWagen): ?>
    <script>new bootstrap.Modal(document.getElementById('wagenModal')).show();</script>
<?php endif; ?>
</body>
</html>
