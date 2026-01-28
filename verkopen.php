<?php
// Verkopen beheer pagina
require_once 'config/config.php';

$auth->requireLogin();
$auth->requirePermissie('verkopen_bekijken');

// Dao's
$verkoopDao = new VerkoopDao($db);
$artikelDao = new ArtikelDao($db);
$klantDao = new KlantDao($db);

$message = '';
$error = '';

// Verwerk acties
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if($action === 'create') {
        $data = [
            'klant_id' => $_POST['klant_id'],
            'artikel_id' => $_POST['artikel_id']
        ];
        if($verkoopDao->create($data)) {
            $message = 'Verkoop succesvol geregistreerd.';
        } else {
            $error = 'Fout bij registreren verkoop.';
        }
    }

    if($action === 'delete') {
        $id = $_POST['id'];
        if($verkoopDao->delete($id)) {
            $message = 'Verkoop succesvol verwijderd.';
        } else {
            $error = 'Fout bij verwijderen verkoop.';
        }
    }
}

// Filter op periode
$startDatum = $_GET['start'] ?? date('Y-m-01');
$eindDatum = $_GET['eind'] ?? date('Y-m-d');

$verkopen = $verkoopDao->getByPeriode($startDatum, $eindDatum);

// Totaal berekenen
$totaalOmzet = 0;
foreach($verkopen as $verkoop) {
    $totaalOmzet += $verkoop['prijs_ex_btw'];
}

// Data voor formulier
$artikelen = $artikelDao->getAll();
$klanten = $klantDao->getAll();
$pageTitle = 'Verkopen';
?>
<?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-cart"></i> Verkopen</h2>
            <?php if($auth->heeftPermissie('verkopen_registreren')): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#verkoopModal">
                <i class="bi bi-plus-lg"></i> Nieuwe Verkoop
            </button>
            <?php endif; ?>
        </div>

        <?php include 'includes/alerts.php'; ?>

        <!-- Filter op periode -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Van datum</label>
                        <input type="date" name="start" class="form-control" value="<?= e($startDatum) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tot datum</label>
                        <input type="date" name="eind" class="form-control" value="<?= e($eindDatum) ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="bi bi-filter"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="verkopen.php" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Totaal omzet card -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6>Totaal omzet (ex BTW)</h6>
                        <h3>&euro; <?= formatPrijs($totaalOmzet) ?></h3>
                        <small><?= count($verkopen) ?> verkopen</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6>Totaal omzet (incl BTW)</h6>
                        <h3>&euro; <?= formatPrijs($totaalOmzet * 1.21) ?></h3>
                        <small>21% BTW</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Verkopen tabel -->
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Datum</th>
                            <th>Artikel</th>
                            <th>Klant</th>
                            <th class="text-end">Prijs (ex BTW)</th>
                            <th class="text-end">Prijs (incl BTW)</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($verkopen)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">Geen verkopen gevonden.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach($verkopen as $verkoop): ?>
                        <tr>
                            <td><?= e($verkoop['id']) ?></td>
                            <td><?= formatDatumTijd($verkoop['verkocht_op']) ?></td>
                            <td><?= e($verkoop['artikel_naam']) ?></td>
                            <td><?= e($verkoop['klant_naam']) ?></td>
                            <td class="text-end">&euro; <?= formatPrijs($verkoop['prijs_ex_btw']) ?></td>
                            <td class="text-end">&euro; <?= formatPrijs($verkoop['prijs_ex_btw'] * 1.21) ?></td>
                            <td>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Weet je het zeker?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $verkoop['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-success fw-bold">
                            <td colspan="4">Totaal</td>
                            <td class="text-end">&euro; <?= formatPrijs($totaalOmzet) ?></td>
                            <td class="text-end">&euro; <?= formatPrijs($totaalOmzet * 1.21) ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="verkoopModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Nieuwe Verkoop Registreren</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">

                        <div class="mb-3">
                            <label for="klant_id" class="form-label">Klant</label>
                            <select class="form-select" id="klant_id" name="klant_id" required>
                                <option value="">Selecteer klant...</option>
                                <?php foreach($klanten as $klant): ?>
                                <option value="<?= $klant->getId() ?>">
                                    <?= e($klant->getNaam()) ?> - <?= e($klant->getPlaats()) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="artikel_id" class="form-label">Artikel</label>
                            <select class="form-select" id="artikel_id" name="artikel_id" required>
                                <option value="">Selecteer artikel...</option>
                                <?php foreach($artikelen as $artikel): ?>
                                <option value="<?= $artikel->getId() ?>">
                                    <?= e($artikel->getNaam()) ?> - &euro;<?= formatPrijs($artikel->getPrijsExBtw()) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Registreren
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
