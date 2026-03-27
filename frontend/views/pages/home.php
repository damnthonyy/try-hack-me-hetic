<?php

declare(strict_types=1);

$pageCss = 'pages/home';
?>
<nav class="home-actions">
    <a href="/login" class="btn btn-primary">Se connecter</a>
    <a href="/register" class="btn btn-link">Créer un compte</a>
</nav>
<hr class="divider-home">

<article class="page page--home">
    <h1 class="title">Bienvenue</h1>
    <p class="lead">Plateforme d’exercices autour de la sécurité web — volontairement sobre pour que les flux (requêtes, session, base de données) restent lisibles dans le code.</p>
    <div class="home-desc">
        <p>Vous pouvez vous inscrire, vous connecter et accéder à un espace personnel. L’objectif du lab est de comprendre ce qui se passe sous le capot, pas seulement de cliquer sur les boutons prévus.</p>
    </div>
    <aside class="home-hint" aria-label="Note pour les curieux">
        <p>Les parcours « officiels » passent par les écrans prévus. En environnement contrôlé, il arrive qu’on vérifie aussi ce que l’application accepte <em>ailleurs</em> — par curiosité méthodique, pas par mégarde.</p>
    </aside>
</article>
