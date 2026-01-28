<?php
// Login pagina
require_once 'config/config.php';

// Als al ingelogd, redirect naar dashboard
if($auth->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

// Verwerk login formulier
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['gebruikersnaam'] ?? '';
    $password = $_POST['wachtwoord'] ?? '';

    if(empty($username) || empty($password)) {
        $error = 'Vul alle velden in.';
    } else {
        if($auth->login($username, $password)) {
            header('Location: index.php');
            exit;
        } else {
            $error = 'Ongeldige gebruikersnaam of wachtwoord.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
            min-height: 100vh;
        }
        .login-card {
            max-width: 400px;
            margin: 0 auto;
            margin-top: 10vh;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <img src="images/logo.png" alt="Logo" height="80">
                        <h4 class="mt-3">Kringloop Centrum Duurzaam</h4>
                        <p class="text-muted">Log in om verder te gaan</p>
                    </div>

                    <?php if($error): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> <?= e($error) ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="gebruikersnaam" class="form-label">Gebruikersnaam</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="gebruikersnaam" name="gebruikersnaam"
                                       value="<?= e($_POST['gebruikersnaam'] ?? '') ?>" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="wachtwoord" class="form-label">Wachtwoord</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="wachtwoord" name="wachtwoord" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-in-right"></i> Inloggen
                        </button>
                    </form>

                    <hr class="my-4">
                    <p class="text-muted text-center small mb-0">
                        <i class="bi bi-info-circle"></i> Neem contact op met de beheerder voor inloggegevens.
                    </p>
                </div>
            </div>

            <p class="text-center text-white mt-3">
                <small>&copy; <?= date('Y') ?> <?= APP_NAME ?></small>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
