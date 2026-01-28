<?php
// Voorraad beheer pagina
require_once 'config/config.php';

$auth->requireLogin();
$auth->requirePermissie('voorraad_beheren');

// Dao's
$voorraadDao = new VoorraadDao($db);
$artikelDao = new ArtikelDao($db);
$statusDao = new StatusDao($db);

$message = '';
$error = '';

// Verwerk acties
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if($action === 'create') {
        $data = [
            'artikel_id' => $_POST['artikel_id'],
            'locatie' => $_POST['locatie'],
            'aantal' => $_POST['aantal'],
            'status_id' => $_POST['status_id']
        ];
        if($voorraadDao->create($data)) {
            $message = 'Voorraad succesvol ingeboekt.';
        } else {
            $error = 'Fout bij inboeken voorraad.';
        }
    }

    if($action === 'update') {
        $id = $_POST['id'];
        $data = [
            'artikel_id' => $_POST['artikel_id'],
            'locatie' => $_POST['locatie'],
            'aantal' => $_POST['aantal'],
            'status_id' => $_POST['status_id']
        ];
        if($voorraadDao->update($id, $data)) {
            $message = 'Voorraad succesvol bijgewerkt.';
        } else {
            $error = 'Fout bij bijwerken voorraad.';
        }
    }

    if($action === 'update_status') {
        $id = $_POST['id'];
        $statusId = $_POST['status_id'];
        if($voorraadDao->updateStatus($id, $statusId)) {
            $message = 'Status succesvol bijgewerkt.';
        } else {
            $error = 'Fout bij bijwerken status.';
        }
    }

    if($action === 'delete') {
        $id = $_POST['id'];
        if($voorraadDao->delete($id)) {
            $message = 'Voorraad succesvol verwijderd.';
        } else {
            $error = 'Fout bij verwijderen voorraad.';
        }
    }
}

// Data ophalen
$voorraadItems = $voorraadDao->getAllWithDetails();
$artikelen = $artikelDao->getAll();
$statussen = $statusDao->getAll();

// Filter op status
$filterStatus = $_GET['status'] ?? '';
if($filterStatus) {
    $voorraadItems = array_filter($voorraadItems, function($item) use ($filterStatus) {
        return $item['status_id'] == $filterStatus;
    });
}

// Edit mode
$editVoorraad = null;
if(isset($_GET['edit'])) {
    $editVoorraad = $voorraadDao->getById($_GET['edit']);
}
$pageTitle = 'Voorraad';
?>
<?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-boxes"></i> Voorraad</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#voorraadModal">
                <i class="bi bi-plus-lg"></i> Inboeken
            </button>
        </div>

        <?php include 'includes/alerts.php'; ?>

        <!-- Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Filter op status</label>
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">Alle statussen</option>
                            <?php foreach($statussen as $status): ?>
                            <option value="<?= $status->getId() ?>" <?= $filterStatus == $status->getId() ? 'selected' : '' ?>>
                                <?= e($status->getStatus()) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <a href="voorraad.php" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Voorraad tabel -->
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Artikel</th>
                            <th>Categorie</th>
                            <th>Locatie</th>
                            <th>Aantal</th>
                            <th>Status</th>
                            <th>Ingeboekt</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($voorraadItems)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">Geen voorraad gevonden.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach($voorraadItems as $item): ?>
                        <tr>
                            <td><?= e($item['id']) ?></td>
                            <td><?= e($item['artikel_naam']) ?></td>
                            <td><?= e($item['categorie_naam'] ?? '-') ?></td>
                            <td><?= e($item['locatie']) ?></td>
                            <td><?= e($item['aantal']) ?></td>
                            <td>
                                <?php
                                $statusKleur = 'secondary';
                                if($item['status_id'] == 3) $statusKleur = 'success';
                                if($item['status_id'] == 2) $statusKleur = 'warning';
                                if($item['status_id'] == 5) $statusKleur = 'danger';
                                ?>
                                <span class="badge bg-<?= $statusKleur ?>"><?= e($item['status_naam']) ?></span>
                            </td>
                            <td><?= formatDatumTijd($item['ingeboekt_op']) ?></td>
                            <td>
                                <!-- Quick status update -->
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <?php foreach($statussen as $status): ?>
                                        <li>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                <input type="hidden" name="status_id" value="<?= $status->getId() ?>">
                                                <button type="submit" class="dropdown-item">
                                                    <?= e($status->getStatus()) ?>
                                                </button>
                                            </form>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <a href="?edit=<?= $item['id'] ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Weet je het zeker?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
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

    <!-- Modal -->
    <div class="modal fade" id="voorraadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <?= $editVoorraad ? 'Voorraad Bewerken' : 'Artikel Inboeken' ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?= $editVoorraad ? 'update' : 'create' ?>">
                        <?php if($editVoorraad): ?>
                        <input type="hidden" name="id" value="<?= $editVoorraad->getId() ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="artikel_id" class="form-label">Artikel</label>
                            <select class="form-select" id="artikel_id" name="artikel_id" required>
                                <option value="">Selecteer artikel...</option>
                                <?php foreach($artikelen as $artikel): ?>
                                <option value="<?= $artikel->getId() ?>"
                                    <?= ($editVoorraad && $editVoorraad->getArtikelId() == $artikel->getId()) ? 'selected' : '' ?>>
                                    <?= e($artikel->getNaam()) ?> - &euro;<?= formatPrijs($artikel->getPrijsExBtw()) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="locatie" class="form-label">Locatie</label>
                            <input type="text" class="form-control" id="locatie" name="locatie" required
                                   placeholder="bijv. Magazijn A-1"
                                   value="<?= $editVoorraad ? e($editVoorraad->getLocatie()) : '' ?>">
                        </div>

                        <div class="mb-3">
                            <label for="aantal" class="form-label">Aantal</label>
                            <input type="number" class="form-control" id="aantal" name="aantal" required min="1"
                                   value="<?= $editVoorraad ? $editVoorraad->getAantal() : '1' ?>">
                        </div>

                        <div class="mb-3">
                            <label for="status_id" class="form-label">Status</label>
                            <select class="form-select" id="status_id" name="status_id" required>
                                <?php foreach($statussen as $status): ?>
                                <option value="<?= $status->getId() ?>"
                                    <?= ($editVoorraad && $editVoorraad->getStatusId() == $status->getId()) ? 'selected' : '' ?>>
                                    <?= e($status->getStatus()) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
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
<?php if($editVoorraad): ?>
    <script>new bootstrap.Modal(document.getElementById('voorraadModal')).show();</script>
<?php endif; ?>
</body>
</html>
