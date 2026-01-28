<?php
// Dashboard - hoofdpagina
require_once 'config/config.php';

// Vereis login
$auth->requireLogin();

// Dao's en data ophalen op basis van permissies
$aantalArtikelen = 0;
$aantalKlanten = 0;
$totaalVoorraad = 0;
$verkopenVandaag = 0;
$recenteVerkopen = [];
$planningVandaag = [];

if($auth->heeftPermissie('artikelen_beheren') || $auth->heeftPermissie('voorraad_beheren')) {
    $artikelDao = new ArtikelDao($db);
    $aantalArtikelen = $artikelDao->count();
}
if($auth->heeftPermissie('klanten_beheren')) {
    $klantDao = new KlantDao($db);
    $aantalKlanten = $klantDao->count();
}
if($auth->heeftPermissie('voorraad_beheren')) {
    $voorraadDao = new VoorraadDao($db);
    $totaalVoorraad = $voorraadDao->getTotaalAantal();
}
if($auth->heeftPermissie('verkopen_bekijken') || $auth->heeftPermissie('verkopen_registreren')) {
    $verkoopDao = new VerkoopDao($db);
    $verkopenVandaag = $verkoopDao->countVandaag();
    $recenteVerkopen = $verkoopDao->getAllWithDetails();
    $recenteVerkopen = array_slice($recenteVerkopen, 0, 5);
}
if($auth->heeftPermissie('planning_beheren')) {
    $planningDao = new PlanningDao($db);
    $planningVandaag = $planningDao->getByDatum(date('Y-m-d'));
}
$pageTitle = 'Dashboard';
?>
<?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
            <span class="text-muted"><?= date('l j F Y') ?></span>
        </div>

        <?php if(isset($_GET['error']) && $_GET['error'] == 'geen_toegang'): ?>
        <div class="alert alert-warning alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i> Je hebt geen toegang tot die pagina.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Statistieken kaarten -->
        <div class="row mb-4">
            <?php if($auth->heeftPermissie('artikelen_beheren') || $auth->heeftPermissie('voorraad_beheren')): ?>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Artikelen</h6>
                                <h2 class="mb-0"><?= $aantalArtikelen ?></h2>
                            </div>
                            <i class="bi bi-box-seam display-4 opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="artikelen.php" class="text-white text-decoration-none small">
                            Bekijk alle <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php if($auth->heeftPermissie('klanten_beheren')): ?>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Klanten</h6>
                                <h2 class="mb-0"><?= $aantalKlanten ?></h2>
                            </div>
                            <i class="bi bi-people display-4 opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="klanten.php" class="text-white text-decoration-none small">
                            Bekijk alle <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php if($auth->heeftPermissie('voorraad_beheren')): ?>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Voorraad</h6>
                                <h2 class="mb-0"><?= $totaalVoorraad ?></h2>
                            </div>
                            <i class="bi bi-boxes display-4 opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="voorraad.php" class="text-dark text-decoration-none small">
                            Bekijk alle <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php if($auth->heeftPermissie('verkopen_bekijken') || $auth->heeftPermissie('verkopen_registreren')): ?>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Verkopen vandaag</h6>
                                <h2 class="mb-0"><?= $verkopenVandaag ?></h2>
                            </div>
                            <i class="bi bi-cart-check display-4 opacity-50"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="verkopen.php" class="text-white text-decoration-none small">
                            Bekijk alle <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="row">
            <?php if($auth->heeftPermissie('verkopen_bekijken') || $auth->heeftPermissie('verkopen_registreren')): ?>
            <!-- Recente verkopen -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recente Verkopen</h5>
                    </div>
                    <div class="card-body">
                        <?php if(empty($recenteVerkopen)): ?>
                        <p class="text-muted text-center">Geen recente verkopen gevonden.</p>
                        <?php else: ?>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Datum</th>
                                    <th>Artikel</th>
                                    <th>Klant</th>
                                    <th class="text-end">Prijs</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recenteVerkopen as $verkoop): ?>
                                <tr>
                                    <td><?= formatDatumTijd($verkoop['verkocht_op']) ?></td>
                                    <td><?= e($verkoop['artikel_naam']) ?></td>
                                    <td><?= e($verkoop['klant_naam']) ?></td>
                                    <td class="text-end">&euro; <?= formatPrijs($verkoop['prijs_ex_btw']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if($auth->heeftPermissie('planning_beheren')): ?>
            <!-- Planning vandaag -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-calendar-day"></i> Planning Vandaag</h5>
                    </div>
                    <div class="card-body">
                        <?php if(empty($planningVandaag)): ?>
                        <p class="text-muted text-center">Geen ritten gepland voor vandaag.</p>
                        <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach($planningVandaag as $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-<?= $item->isOphalen() ? 'primary' : 'success' ?> me-2">
                                        <?= $item->isOphalen() ? 'Ophalen' : 'Bezorgen' ?>
                                    </span>
                                    <small><?= date('H:i', strtotime($item->getAfspraakOp())) ?></small>
                                </div>
                                <span class="text-muted small"><?= e($item->getKenteken()) ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="planning.php" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-calendar-week"></i> Volledige planning
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
