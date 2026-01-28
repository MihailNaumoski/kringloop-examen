<?php
// Artikelen beheer pagina
require_once 'config/config.php';

$auth->requireLogin();
$auth->requirePermissie('artikelen_beheren');

// Dao's
$artikelDao = new ArtikelDao($db);
$categorieDao = new CategorieDao($db);

$message = '';
$error = '';

// Verwerk acties
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if($action === 'create') {
        $ean = trim($_POST['ean_nummer'] ?? '');
        if($ean !== '' && strlen($ean) > 13) {
            $error = 'EAN-nummer mag maximaal 13 tekens bevatten.';
        }
        if(empty($error)) {
            $data = [
                'naam' => trim($_POST['naam']),
                'categorie_id' => $_POST['categorie_id'],
                'omschrijving' => trim($_POST['omschrijving'] ?? ''),
                'merk' => trim($_POST['merk'] ?? ''),
                'kleur' => trim($_POST['kleur'] ?? ''),
                'afmeting_maat' => trim($_POST['afmeting_maat'] ?? ''),
                'ean_nummer' => $ean,
                'prijs_ex_btw' => $_POST['prijs_ex_btw']
            ];
            if($artikelDao->create($data)) {
                $message = 'Artikel succesvol toegevoegd.';
            } else {
                $error = 'Fout bij toevoegen artikel.';
            }
        }
    }

    if($action === 'update') {
        $id = $_POST['id'];
        $ean = trim($_POST['ean_nummer'] ?? '');
        if($ean !== '' && strlen($ean) > 13) {
            $error = 'EAN-nummer mag maximaal 13 tekens bevatten.';
        }
        if(empty($error)) {
            $data = [
                'naam' => trim($_POST['naam']),
                'categorie_id' => $_POST['categorie_id'],
                'omschrijving' => trim($_POST['omschrijving'] ?? ''),
                'merk' => trim($_POST['merk'] ?? ''),
                'kleur' => trim($_POST['kleur'] ?? ''),
                'afmeting_maat' => trim($_POST['afmeting_maat'] ?? ''),
                'ean_nummer' => $ean,
                'prijs_ex_btw' => $_POST['prijs_ex_btw']
            ];
            if($artikelDao->update($id, $data)) {
                $message = 'Artikel succesvol bijgewerkt.';
            } else {
                $error = 'Fout bij bijwerken artikel.';
            }
        }
    }

    if($action === 'delete') {
        $id = $_POST['id'];
        if($artikelDao->delete($id)) {
            $message = 'Artikel succesvol verwijderd.';
        } else {
            $error = 'Fout bij verwijderen artikel. Mogelijk in gebruik.';
        }
    }
}

// Data ophalen
$artikelen = $artikelDao->getAllWithCategorie();
$categorieen = $categorieDao->getAll();

// Zoeken
$zoekterm = $_GET['zoek'] ?? '';
if($zoekterm) {
    $artikelen = [];
    foreach($artikelDao->zoek($zoekterm) as $artikel) {
        $artikelen[] = [
            'id' => $artikel->getId(),
            'naam' => $artikel->getNaam(),
            'categorie_id' => $artikel->getCategorieId(),
            'prijs_ex_btw' => $artikel->getPrijsExBtw(),
            'categorie_naam' => ''
        ];
    }
}

// Edit mode
$editArtikel = null;
if(isset($_GET['edit'])) {
    $editArtikel = $artikelDao->getById($_GET['edit']);
}
$pageTitle = 'Artikelen';
?>
<?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-box-seam"></i> Artikelen</h2>
            <?php if($editArtikel): ?>
            <a href="artikelen.php" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Nieuw Artikel
            </a>
            <?php else: ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#artikelModal">
                <i class="bi bi-plus-lg"></i> Nieuw Artikel
            </button>
            <?php endif; ?>
        </div>

        <?php include 'includes/alerts.php'; ?>

        <!-- Zoekbalk -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-10">
                        <input type="text" name="zoek" class="form-control" placeholder="Zoek op artikelnaam..."
                               value="<?= e($zoekterm) ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="bi bi-search"></i> Zoeken
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Artikelen tabel -->
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Naam</th>
                            <th>Categorie</th>
                            <th class="text-end">Prijs (ex BTW)</th>
                            <th class="text-end">Prijs (incl BTW)</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($artikelen)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Geen artikelen gevonden.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach($artikelen as $artikel): ?>
                        <tr>
                            <td><?= e($artikel['id']) ?></td>
                            <td><?= e($artikel['naam']) ?></td>
                            <td><?= e($artikel['categorie_naam'] ?? '-') ?></td>
                            <td class="text-end">&euro; <?= formatPrijs($artikel['prijs_ex_btw']) ?></td>
                            <td class="text-end">&euro; <?= formatPrijs($artikel['prijs_ex_btw'] * 1.21) ?></td>
                            <td>
                                <a href="?edit=<?= $artikel['id'] ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Weet je het zeker?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $artikel['id'] ?>">
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
    <div class="modal fade" id="artikelModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <?= $editArtikel ? 'Artikel Bewerken' : 'Nieuw Artikel' ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?= $editArtikel ? 'update' : 'create' ?>">
                        <?php if($editArtikel): ?>
                        <input type="hidden" name="id" value="<?= $editArtikel->getId() ?>">
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="naam" class="form-label">Naam</label>
                                    <input type="text" class="form-control" id="naam" name="naam" required
                                           value="<?= $editArtikel ? e($editArtikel->getNaam()) : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ean_nummer" class="form-label">EAN-nummer</label>
                                    <input type="text" class="form-control" id="ean_nummer" name="ean_nummer"
                                           maxlength="13" placeholder="13 cijfers"
                                           value="<?= $editArtikel ? e($editArtikel->getEanNummer()) : '' ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="omschrijving" class="form-label">Omschrijving</label>
                            <textarea class="form-control" id="omschrijving" name="omschrijving" rows="2"><?= $editArtikel ? e($editArtikel->getOmschrijving()) : '' ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="categorie_id" class="form-label">Categorie</label>
                                    <select class="form-select" id="categorie_id" name="categorie_id" required>
                                        <option value="">Selecteer categorie...</option>
                                        <?php foreach($categorieen as $cat): ?>
                                        <option value="<?= $cat->getId() ?>"
                                            <?= ($editArtikel && $editArtikel->getCategorieId() == $cat->getId()) ? 'selected' : '' ?>>
                                            <?= e($cat->getCategorie()) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="merk" class="form-label">Merk</label>
                                    <input type="text" class="form-control" id="merk" name="merk"
                                           value="<?= $editArtikel ? e($editArtikel->getMerk()) : '' ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kleur" class="form-label">Kleur</label>
                                    <input type="text" class="form-control" id="kleur" name="kleur"
                                           value="<?= $editArtikel ? e($editArtikel->getKleur()) : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="afmeting_maat" class="form-label">Afmeting/Maat</label>
                                    <input type="text" class="form-control" id="afmeting_maat" name="afmeting_maat"
                                           value="<?= $editArtikel ? e($editArtikel->getAfmetingMaat()) : '' ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="prijs_ex_btw" class="form-label">Prijs (ex BTW)</label>
                            <div class="input-group">
                                <span class="input-group-text">&euro;</span>
                                <input type="number" step="0.01" class="form-control" id="prijs_ex_btw"
                                       name="prijs_ex_btw" required
                                       value="<?= $editArtikel ? $editArtikel->getPrijsExBtw() : '' ?>">
                            </div>
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
<?php if($editArtikel): ?>
    <script>new bootstrap.Modal(document.getElementById('artikelModal')).show();</script>
<?php endif; ?>
</body>
</html>
