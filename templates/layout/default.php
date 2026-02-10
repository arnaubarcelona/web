<?php
$cakeDescription = 'CakePHP: the rapid development php framework';
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $cakeDescription ?>: <?= $this->fetch('title') ?></title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css(['normalize.min', 'milligram.min', 'fonts', 'cake']) ?>
    <?= $this->Html->css('layout_custom') ?>  <?php // <-- afegeix el teu CSS ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>

    <!-- TOPBAR FIXA -->
        <header class="app-topbar" id="appTopbar">

    <!-- Bloc centrat: logo + xarxes -->
    <div class="app-topbar__center">
        <?= $this->Html->image('logoGran.png', [
            'alt' => 'CFA Guinardó',
            'class' => 'app-topbar__logo'
        ]) ?>

        <div class="app-topbar__social">

            <?= $this->Html->link(
                $this->Html->image('instagram.png', [
                    'alt' => 'Instagram',
                    'class' => 'app-topbar__socialIcon'
                ]),
                'https://www.instagram.com/cfaguinardo',
                ['escape' => false, 'target' => '_blank', 'rel' => 'noopener']
            ) ?>

            <!-- BOTÓ MENU (sidebar) -->
            <button
                class="app-topbar__menuImgBtn"
                id="openSidebarBtn"
                aria-label="Obrir menú"
                type="button"
            >
                MENU
            </button>


            <?= $this->Html->link(
                $this->Html->image('facebook.png', [
                    'alt' => 'Facebook',
                    'class' => 'app-topbar__socialIcon'
                ]),
                'https://www.facebook.com/people/Cfa-Guinardo/61560117734842/',
                ['escape' => false, 'target' => '_blank', 'rel' => 'noopener']
            ) ?>

        </div>

    </div>

    <!-- Botó Inscriu-te (només desktop) -->
    <?= $this->Html->link(
        'INSCRIU-TE',
        'http://www.cfaguinardo.cat/gestioalumnes/students/alta-inici',
        ['class' => 'app-topbar__cta', 'target' => '_blank', 'rel' => 'noopener']
    ) ?>
</header>



    <!-- SIDEBAR FIXA -->
    <aside class="app-sidebar" id="appSidebar" aria-hidden="false">
        <!-- Botó tancar (només quan és overlay en mòbil) -->
        <button class="app-sidebar__close" id="closeSidebarBtn" type="button" aria-label="Tancar menú">
            ✕
        </button>

        <div class="app-sidebar__content">
            <?= $this->element('botoimgtext', [
                'text' => 'Menú',
                'image' => 'menu.png',
                'link' => ['controller' => 'Pages', 'action' => 'display', 'home'],
                'title' => 'Menú',
                'color' => 'blaumari'
            ]) ?>
        </div>
    </aside>

    <!-- FONS FOSC quan la sidebar està oberta en mòbil -->
    <div class="app-backdrop" id="appBackdrop" hidden></div>

    <!-- CONTINGUT -->
    <main class="app-main">
        <div class="app-container">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>

    <footer></footer>

    <!-- JS (inline, o posa-ho a un fitxer .js si prefereixes) -->
    <script>
        (function () {
            const sidebar = document.getElementById('appSidebar');
            const openBtn = document.getElementById('openSidebarBtn');
            const closeBtn = document.getElementById('closeSidebarBtn');
            const backdrop = document.getElementById('appBackdrop');

            function openSidebar() {
                document.body.classList.add('sidebar-open');
                backdrop.hidden = false;
                sidebar.setAttribute('aria-hidden', 'false');
            }

            function closeSidebar() {
                document.body.classList.remove('sidebar-open');
                backdrop.hidden = true;
                sidebar.setAttribute('aria-hidden', 'true');
            }

            openBtn?.addEventListener('click', openSidebar);
            closeBtn?.addEventListener('click', closeSidebar);
            backdrop?.addEventListener('click', closeSidebar);

            // ESC per tancar
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && document.body.classList.contains('sidebar-open')) {
                    closeSidebar();
                }
            });
        })();
    </script>

</body>
</html>
