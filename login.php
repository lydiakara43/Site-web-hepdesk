<?php
/**
 * login.php — Page de connexion
 * Traite le formulaire POST et redirige selon le rôle.
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Si déjà connecté → tableau de bord
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = post('username');
    $password = post('password');

    // Validation basique
    if (empty($username) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } elseif (login($username, $password)) {
        // Connexion réussie → redirection
        setFlash('success', 'Bienvenue, ' . $_SESSION['name'] . ' !');
        redirect('index.php');
    } else {
        $error = 'Identifiant ou mot de passe incorrect.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Helpdesk</title>
    <link rel="stylesheet" href="/assets/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body class="login-body">

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-logo">
            <span class="login-icon">🎫</span>
            <h1>Helpdesk</h1>
            <p class="login-subtitle">Plateforme de support étudiant</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error">
            <?= e($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="login.php" novalidate>
            <div class="form-group">
                <label for="username">Identifiant</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="alice, bob, tuteur…"
                    value="<?= e(post('username')) ?>"
                    autocomplete="username"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary btn-full">
                Se connecter
            </button>
        </form>

        <div class="login-demo">
            <p>Comptes de démonstration — mot de passe : <code>password</code></p>
            <div class="demo-accounts">
                <span>🎓 alice / bob</span>
                <span>👨‍🏫 tuteur</span>
            </div>
        </div>
    </div>
</div>

<script src="/assets/script.js"></script>
</body>
</html>
