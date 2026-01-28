<?php
// Rapportages - maandoverzichten
require_once 'config/config.php';

$auth->requireLogin();
$auth->requirePermissie('maandoverzicht_bekijken');

// Dao's
$verkoopDao = new VerkoopDao($db);

// Huidig jaar en maand
$jaar = $_GET['jaar'] ?? date('Y');
$maand = $_GET['maand'] ?? date('m');

// Maandoverzicht ophalen
$verkopen = $verkoopDao->getMaandOverzicht($jaar, $maand);
$omzetTotaal = $verkoopDao->getOmzetPerMaand($jaar, $maand);
$omzetPerCategorie = $verkoopDao->getOmzetPerCategorie($jaar, $maand);

// Maandnamen
$maandNamen = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maart', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Augustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'December'
];
$pageTitle = 'Rapportages';
?>
<?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-graph-up"></i> Maandoverzicht</h2>
        </div>

        <!-- Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Maand</label>
                        <select name="maand" class="form-select">
                            <?php foreach($maandNamen as $num => $naam): ?>
                            <option value="<?= str_pad($num, 2, '0', STR_PAD_LEFT) ?>"
                                <?= $maand == str_pad($num, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>>
                                <?= $naam ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jaar</label>
                        <select name="jaar" class="form-select">
                            <?php for($j = date('Y'); $j >= 2020; $j--): ?>
                            <option value="<?= $j ?>" <?= $jaar == $j ? 'selected' : '' ?>><?= $j ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Bekijken
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Overzicht kaarten -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h6>Totale Omzet (ex BTW)</h6>
                        <h2>&euro; <?= formatPrijs($omzetTotaal) ?></h2>
                        <small><?= $maandNamen[(int)$maand] ?> <?= $jaar ?></small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h6>Totale Omzet (incl BTW)</h6>
                        <h2>&euro; <?= formatPrijs($omzetTotaal * 1.21) ?></h2>
                        <small>21% BTW</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h6>Aantal Verkopen</h6>
                        <h2><?= count($verkopen) ?></h2>
                        <small>deze maand</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Omzet per categorie -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Omzet per Categorie</h5>
                    </div>
                    <div class="card-body">
                        <?php if(empty($omzetPerCategorie)): ?>
                        <p class="text-muted text-center">Geen data beschikbaar.</p>
                        <?php else: ?>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Categorie</th>
                                    <th class="text-center">Aantal</th>
                                    <th class="text-end">Omzet</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($omzetPerCategorie as $cat): ?>
                                <tr>
                                    <td><?= e($cat['categorie']) ?></td>
                                    <td class="text-center"><?= $cat['aantal'] ?></td>
                                    <td class="text-end">&euro; <?= formatPrijs($cat['totaal']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Verkopen lijst -->
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-list-ul"></i> Verkopen deze maand</h5>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        <?php if(empty($verkopen)): ?>
                        <p class="text-muted text-center">Geen verkopen deze maand.</p>
                        <?php else: ?>
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Datum</th>
                                    <th>Artikel</th>
                                    <th>Categorie</th>
                                    <th class="text-end">Prijs</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($verkopen as $verkoop): ?>
                                <tr>
                                    <td><?= formatDatum($verkoop['verkocht_op']) ?></td>
                                    <td><?= e($verkoop['artikel_naam']) ?></td>
                                    <td><small class="text-muted"><?= e($verkoop['categorie_naam'] ?? '-') ?></small></td>
                                    <td class="text-end">&euro; <?= formatPrijs($verkoop['prijs_ex_btw']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
