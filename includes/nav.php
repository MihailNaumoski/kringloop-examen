<?php
// Navigatie menu - rol-gebaseerd
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="images/logo.png" alt="KCD Logo" height="40" class="me-2">
            <span>Kringloop Centrum Duurzaam</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'index.php' ? 'active' : '' ?>" href="index.php">
                        <i class="bi bi-house"></i> Dashboard
                    </a>
                </li>

                <?php if($auth->heeftPermissie('voorraad_beheren') || $auth->heeftPermissie('artikelen_beheren')): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= in_array($currentPage, ['voorraad.php', 'artikelen.php', 'categorieen.php']) ? 'active' : '' ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-box-seam"></i> Voorraadbeheer
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="voorraad.php">Voorraad</a></li>
                        <li><a class="dropdown-item" href="artikelen.php">Artikelen</a></li>
                        <?php if($auth->heeftPermissie('categorieen_beheren')): ?>
                        <li><a class="dropdown-item" href="categorieen.php">Categorieen</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if($auth->heeftPermissie('klanten_beheren') || $auth->heeftPermissie('persoonsgegevens_beheren')): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'klanten.php' ? 'active' : '' ?>" href="klanten.php">
                        <i class="bi bi-people"></i> Klanten
                    </a>
                </li>
                <?php endif; ?>

                <?php if($auth->heeftPermissie('persoonsgegevens_beheren')): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'persoonsgegevens.php' ? 'active' : '' ?>" href="persoonsgegevens.php">
                        <i class="bi bi-person-lines-fill"></i> Persoonsgegevens
                    </a>
                </li>
                <?php endif; ?>

                <?php if($auth->heeftPermissie('verkopen_bekijken') || $auth->heeftPermissie('verkopen_registreren')): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'verkopen.php' ? 'active' : '' ?>" href="verkopen.php">
                        <i class="bi bi-cart"></i> Verkopen
                    </a>
                </li>
                <?php endif; ?>

                <?php if($auth->heeftPermissie('planning_beheren')): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'planning.php' ? 'active' : '' ?>" href="planning.php">
                        <i class="bi bi-calendar"></i> Planning
                    </a>
                </li>
                <?php endif; ?>

                <?php if($auth->heeftPermissie('planning_beheren')): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'wagens.php' ? 'active' : '' ?>" href="wagens.php">
                        <i class="bi bi-truck"></i> Wagens
                    </a>
                </li>
                <?php endif; ?>

                <?php if($auth->heeftPermissie('maandoverzicht_bekijken')): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'rapportages.php' ? 'active' : '' ?>" href="rapportages.php">
                        <i class="bi bi-graph-up"></i> Rapportages
                    </a>
                </li>
                <?php endif; ?>

                <?php if($auth->heeftPermissie('gebruikers_beheren')): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage == 'gebruikers.php' ? 'active' : '' ?>" href="gebruikers.php">
                        <i class="bi bi-person-gear"></i> Gebruikers
                    </a>
                </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <?= e($_SESSION['gebruikersnaam'] ?? 'Gebruiker') ?>
                        <span class="badge bg-light text-primary"><?= e($_SESSION['rollen'] ?? '') ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profiel.php"><i class="bi bi-person"></i> Profiel</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Uitloggen</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
