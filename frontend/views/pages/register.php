<?php

declare(strict_types=1);

$pageCss = 'pages/auth';

$authErrors = [
    'required' => 'Pseudo, email et mot de passe obligatoires.',
    'mismatch' => 'Les mots de passe ne correspondent pas.',
    'email' => 'Adresse email invalide.',
    'password' => 'Le mot de passe doit faire au moins 6 caractères.',
    'invalid_username' => 'Le pseudo doit faire entre 3 et 64 caractères (lettres, chiffres et underscore uniquement).',
    'taken' => 'Cette adresse email est déjà utilisée.',
    'username_taken' => 'Ce pseudo est déjà pris.',
    'server' => 'Erreur serveur. Réessaie plus tard.',
];
$authErrorCode = isset($_GET['error']) ? (string)$_GET['error'] : '';
$authErrorMessage = $authErrorCode !== '' && isset($authErrors[$authErrorCode])
    ? $authErrors[$authErrorCode]
    : ($authErrorCode !== '' ? 'Inscription impossible.' : '');
?>
<section class="auth">
    <header class="auth__header">
        <h1 class="title">Inscription</h1>
        <p class="lead">Crée un compte pour continuer.</p>
    </header>

    <?php if ($authErrorMessage !== ''): ?>
        <div class="auth__alert auth__alert--error" role="alert"><?= htmlspecialchars($authErrorMessage, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form class="auth__form" action="/register" method="post">
        <div class="form-group">
            <label class="form-label" for="reg-username">Pseudo</label>
            <input
                type="text"
                class="form-control"
                id="reg-username"
                name="username"
                autocomplete="username"
                required
                minlength="3"
                maxlength="64"
                pattern="[a-zA-Z0-9_]+"
                title="Lettres, chiffres et underscore uniquement"
            >
        </div>
        <div class="form-group">
            <label class="form-label" for="reg-email">Email</label>
            <input type="email" class="form-control" id="reg-email" name="email" autocomplete="email" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="reg-password">Mot de passe</label>
            <input type="password" class="form-control" id="reg-password" name="password" autocomplete="new-password" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirmation du mot de passe</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
        </div>
        <button type="submit" class="btn btn-primary">S’inscrire</button>
    </form>

    <p class="auth__footer">Déjà un compte ? <a href="/auth/login">Se connecter</a></p>
</section>
