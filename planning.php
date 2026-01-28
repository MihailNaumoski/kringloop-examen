<?php
// Planning ritten pagina
require_once 'config/config.php';

$auth->requireLogin();
$auth->requirePermissie('planning_beheren');

// Dao's
$planningDao = new PlanningDao($db);
$artikelDao = new ArtikelDao($db);
$klantDao = new KlantDao($db);
$wagenDao = new WagenDao($db);

$message = '';
$error = '';

// Verwerk acties
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Alleen als gebruiker mag plannen
    if($auth->heeftPermissie('planning_beheren')) {
        if($action === 'create') {
            $data = [
                'artikel_id' => $_POST['artikel_id'],
                'klant_id' => $_POST['klant_id'],
                'kenteken' => strtoupper($_POST['kenteken']),
                'ophalen_of_bezorgen' => $_POST['ophalen_of_bezorgen'],
                'afspraak_op' => $_POST['afspraak_datum'] . ' ' . $_POST['afspraak_tijd']
            ];
            if($planningDao->create($data)) {
                $message = 'Planning succesvol toegevoegd.';
            } else {
                $error = 'Fout bij toevoegen planning.';
            }
        }

        if($action === 'update') {
            $id = $_POST['id'];
            $data = [
                'artikel_id' => $_POST['artikel_id'],
                'klant_id' => $_POST['klant_id'],
                'kenteken' => strtoupper($_POST['kenteken']),
                'ophalen_of_bezorgen' => $_POST['ophalen_of_bezorgen'],
                'afspraak_op' => $_POST['afspraak_datum'] . ' ' . $_POST['afspraak_tijd']
            ];
            if($planningDao->update($id, $data)) {
                $message = 'Planning succesvol bijgewerkt.';
            } else {
                $error = 'Fout bij bijwerken planning.';
            }
        }

        if($action === 'delete') {
            $id = $_POST['id'];
            if($planningDao->delete($id)) {
                $message = 'Planning succesvol verwijderd.';
            } else {
                $error = 'Fout bij verwijderen planning.';
            }
        }
    }
}

// Data ophalen
$planningItems = $planningDao->getAllWithDetails();
$artikelen = $artikelDao->getAll();
$klanten = $klantDao->getAll();
$wagens = $wagenDao->getAll();

// Filter op type
$filterType = $_GET['type'] ?? '';
if($filterType) {
    $planningItems = array_filter($planningItems, function($item) use ($filterType) {
        return $item['ophalen_of_bezorgen'] === $filterType;
    });
}

// Filter op datum
$filterDatum = $_GET['datum'] ?? '';
if($filterDatum) {
    $planningItems = array_filter($planningItems, function($item) use ($filterDatum) {
        return date('Y-m-d', strtotime($item['afspraak_op'])) === $filterDatum;
    });
}

// Edit mode
$editPlanning = null;
if(isset($_GET['edit'])) {
    $editPlanning = $planningDao->getById($_GET['edit']);
}

$kanBewerken = $auth->heeftPermissie('planning_beheren');
$pageTitle = 'Planning';
?>
<?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-calendar"></i> Planning Ritten</h2>
            <?php if($kanBewerken): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#planningModal">
                <i class="bi bi-plus-lg"></i> Nieuwe Rit
            </button>
            <?php endif; ?>
        </div>

        <?php include 'includes/alerts.php'; ?>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="">Alle types</option>
                            <option value="ophalen" <?= $filterType === 'ophalen' ? 'selected' : '' ?>>Ophalen</option>
                            <option value="bezorgen" <?= $filterType === 'bezorgen' ? 'selected' : '' ?>>Bezorgen</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Datum</label>
                        <input type="date" name="datum" class="form-control" value="<?= e($filterDatum) ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="bi bi-filter"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="planning.php" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Planning tabel -->
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Datum/Tijd</th>
                            <th>Type</th>
                            <th>Klant</th>
                            <th>Adres</th>
                            <th>Artikel</th>
                            <th>Kenteken</th>
                            <?php if($kanBewerken): ?>
                            <th>Acties</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($planningItems)): ?>
                        <tr>
                            <td colspan="<?= $kanBewerken ? '8' : '7' ?>" class="text-center text-muted">Geen planning gevonden.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach($planningItems as $item): ?>
                        <tr>
                            <td><?= e($item['id']) ?></td>
                            <td><?= formatDatumTijd($item['afspraak_op']) ?></td>
                            <td>
                                <span class="badge bg-<?= $item['ophalen_of_bezorgen'] === 'ophalen' ? 'primary' : 'success' ?>">
                                    <?= $item['ophalen_of_bezorgen'] === 'ophalen' ? 'Ophalen' : 'Bezorgen' ?>
                                </span>
                            </td>
                            <td><?= e($item['klant_naam']) ?></td>
                            <td><?= e($item['adres']) ?>, <?= e($item['plaats']) ?></td>
                            <td><?= e($item['artikel_naam']) ?></td>
                            <td><code><?= e($item['kenteken']) ?></code></td>
                            <?php if($kanBewerken): ?>
                            <td>
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
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <?php if($kanBewerken): ?>
    <div class="modal fade" id="planningModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <?= $editPlanning ? 'Rit Bewerken' : 'Nieuwe Rit Plannen' ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?= $editPlanning ? 'update' : 'create' ?>">
                        <?php if($editPlanning): ?>
                        <input type="hidden" name="id" value="<?= $editPlanning->getId() ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="ophalen_of_bezorgen" class="form-label">Type</label>
                            <select class="form-select" id="ophalen_of_bezorgen" name="ophalen_of_bezorgen" required>
                                <option value="ophalen" <?= ($editPlanning && $editPlanning->isOphalen()) ? 'selected' : '' ?>>Ophalen</option>
                                <option value="bezorgen" <?= ($editPlanning && $editPlanning->isBezorgen()) ? 'selected' : '' ?>>Bezorgen</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="klant_id" class="form-label">Klant</label>
                            <select class="form-select" id="klant_id" name="klant_id" required>
                                <option value="">Selecteer klant...</option>
                                <?php foreach($klanten as $klant): ?>
                                <option value="<?= $klant->getId() ?>"
                                    <?= ($editPlanning && $editPlanning->getKlantId() == $klant->getId()) ? 'selected' : '' ?>>
                                    <?= e($klant->getNaam()) ?> - <?= e($klant->getAdres()) ?>, <?= e($klant->getPlaats()) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="artikel_id" class="form-label">Artikel</label>
                            <select class="form-select" id="artikel_id" name="artikel_id" required>
                                <option value="">Selecteer artikel...</option>
                                <?php foreach($artikelen as $artikel): ?>
                                <option value="<?= $artikel->getId() ?>"
                                    <?= ($editPlanning && $editPlanning->getArtikelId() == $artikel->getId()) ? 'selected' : '' ?>>
                                    <?= e($artikel->getNaam()) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="afspraak_datum" class="form-label">Datum</label>
                                    <input type="date" class="form-control" id="afspraak_datum" name="afspraak_datum" required
                                           value="<?= $editPlanning ? date('Y-m-d', strtotime($editPlanning->getAfspraakOp())) : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="afspraak_tijd" class="form-label">Tijd</label>
                                    <input type="time" class="form-control" id="afspraak_tijd" name="afspraak_tijd" required
                                           value="<?= $editPlanning ? date('H:i', strtotime($editPlanning->getAfspraakOp())) : '' ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="kenteken" class="form-label">Wagen</label>
                            <select class="form-select" id="kenteken" name="kenteken" required>
                                <option value="">Selecteer wagen...</option>
                                <?php foreach($wagens as $wagen): ?>
                                <option value="<?= e($wagen->getKenteken()) ?>"
                                    <?= ($editPlanning && $editPlanning->getKenteken() == $wagen->getKenteken()) ? 'selected' : '' ?>>
                                    <?= e($wagen->getKenteken()) ?> <?= $wagen->getOmschrijving() ? '- ' . e($wagen->getOmschrijving()) : '' ?>
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
    <?php endif; ?>

<?php include 'includes/footer.php'; ?>
<?php if($editPlanning): ?>
    <script>new bootstrap.Modal(document.getElementById('planningModal')).show();</script>
<?php endif; ?>
</body>
</html>
