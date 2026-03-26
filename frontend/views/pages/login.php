<?php

declare(strict_types=1);

$pageCss = 'pages/auth';

$authErrors = [
    'required' => 'Email et mot de passe obligatoires.',
    'invalid' => 'Identifiants incorrects.',
    'server' => 'Erreur serveur. Réessaie plus tard.',
];
$authErrorCode = isset($_GET['error']) ? (string)$_GET['error'] : '';
$authErrorMessage = $authErrorCode !== '' && isset($authErrors[$authErrorCode])
    ? $authErrors[$authErrorCode]
    : ($authErrorCode !== '' ? 'Connexion impossible.' : '');
?>
<section class="auth">
    <header class="auth__header">
        <h1 class="title">Connexion</h1>
        <p class="lead">Connecte-toi pour accéder à ton compte.</p>
    </header>

    <?php if ($authErrorMessage !== ''): ?>
        <div class="auth__alert auth__alert--error" role="alert"><?= htmlspecialchars($authErrorMessage, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form class="auth__form" action="/login" method="post">
        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" autocomplete="email" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="password">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" required>
        </div>
        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>

    <p class="auth__footer">Pas de compte ? <a href="/auth/register">S’inscrire</a></p>
</section>
