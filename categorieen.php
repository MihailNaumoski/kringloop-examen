<?php
// Categorieen beheer pagina
require_once 'config/config.php';

$auth->requireLogin();
$auth->requirePermissie('categorieen_beheren');

// Dao's
$categorieDao = new CategorieDao($db);

$message = '';
$error = '';

// Verwerk acties
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if($action === 'create') {
        $data = [
            'code' => $_POST['code'] ?? '',
            'categorie' => $_POST['categorie']
        ];
        if($categorieDao->create($data)) {
            $message = 'Categorie succesvol toegevoegd.';
        } else {
            $error = 'Fout bij toevoegen categorie.';
        }
    }

    if($action === 'update') {
        $id = $_POST['id'];
        $data = [
            'code' => $_POST['code'] ?? '',
            'categorie' => $_POST['categorie']
        ];
        if($categorieDao->update($id, $data)) {
            $message = 'Categorie succesvol bijgewerkt.';
        } else {
            $error = 'Fout bij bijwerken categorie.';
        }
    }

    if($action === 'delete') {
        $id = $_POST['id'];
        $aantalArtikelen = $categorieDao->countArtikelen($id);
        if($aantalArtikelen > 0) {
            $error = 'Kan categorie niet verwijderen. Er zijn nog ' . $aantalArtikelen . ' artikelen gekoppeld.';
        } else {
            if($categorieDao->delete($id)) {
                $message = 'Categorie succesvol verwijderd.';
            } else {
                $error = 'Fout bij verwijderen categorie.';
            }
        }
    }
}

// Data ophalen
$categorieen = $categorieDao->getAll();

// Edit mode
$editCategorie = null;
if(isset($_GET['edit'])) {
    $editCategorie = $categorieDao->getById($_GET['edit']);
}
$pageTitle = 'Categorieen';
?>
<?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-tags"></i> Categorieen</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categorieModal">
                <i class="bi bi-plus-lg"></i> Nieuwe Categorie
            </button>
        </div>

        <?php include 'includes/alerts.php'; ?>

        <!-- Categorieen tabel -->
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Categorie</th>
                            <th>Aantal Artikelen</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($categorieen as $categorie): ?>
                        <?php $aantalArtikelen = $categorieDao->countArtikelen($categorie->getId()); ?>
                        <tr>
                            <td><?= e($categorie->getId()) ?></td>
                            <td><code><?= e($categorie->getCode() ?: '-') ?></code></td>
                            <td><?= e($categorie->getCategorie()) ?></td>
                            <td><span class="badge bg-secondary"><?= $aantalArtikelen ?></span></td>
                            <td>
                                <a href="?edit=<?= $categorie->getId() ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Weet je het zeker?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $categorie->getId() ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" <?= $aantalArtikelen > 0 ? 'disabled' : '' ?>>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="categorieModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <?= $editCategorie ? 'Categorie Bewerken' : 'Nieuwe Categorie' ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?= $editCategorie ? 'update' : 'create' ?>">
                        <?php if($editCategorie): ?>
                        <input type="hidden" name="id" value="<?= $editCategorie->getId() ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="code" class="form-label">Code</label>
                            <input type="text" class="form-control" id="code" name="code" maxlength="50"
                                   placeholder="bijv. KLD, MBL, WIT"
                                   value="<?= $editCategorie ? e($editCategorie->getCode()) : '' ?>">
                        </div>

                        <div class="mb-3">
                            <label for="categorie" class="form-label">Categorie naam</label>
                            <input type="text" class="form-control" id="categorie" name="categorie" required
                                   value="<?= $editCategorie ? e($editCategorie->getCategorie()) : '' ?>">
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
<?php if($editCategorie): ?>
    <script>new bootstrap.Modal(document.getElementById('categorieModal')).show();</script>
<?php endif; ?>
</body>
</html>
