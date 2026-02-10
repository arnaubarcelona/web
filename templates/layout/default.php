<?php
$cakeDescription = 'CakePHP: the rapid development php framework';

/** @var \App\Model\Table\PaginesTable $Pagines */
$Pagines = \Cake\ORM\TableRegistry::getTableLocator()->get('Pagines');

$pages = $Pagines->find()
    ->select(['id', 'title', 'order_code', 'visible'])
    ->where(['visible' => 1])
    ->all()
    ->toList();

/**
 * Ordenació "natural" per order_code: 1, 1.1, 1.2, 2, 10, 10.1...
 */
usort($pages, function ($a, $b) {
    $pa = array_map('intval', explode('.', (string)$a->order_code));
    $pb = array_map('intval', explode('.', (string)$b->order_code));

    $len = max(count($pa), count($pb));
    for ($i = 0; $i < $len; $i++) {
        $va = $pa[$i] ?? 0;
        $vb = $pb[$i] ?? 0;
        if ($va !== $vb) {
            return $va <=> $vb;
        }
    }
    return 0;
});

/**
 * nivell = nº de parts separades per punt. Ex: "1"=1, "1.1"=2
 * (closure per evitar "Cannot redeclare" si el layout es carrega més d'un cop)
 */
$pageLevel = function (string $orderCode): int {
    $orderCode = trim($orderCode);
    return $orderCode === '' ? 1 : count(explode('.', $orderCode));
};
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($cakeDescription) ?>: <?= $this->fetch('title') ?></title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css(['normalize.min', 'milligram.min', 'fonts', 'cake']) ?>
    <?= $this->Html->css('layout_custom') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>

<!-- TOPBAR FIXA -->
<header class="app-topbar" id="appTopbar">
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

    <?= $this->Html->link(
        'INSCRIU-TE',
        'http://www.cfaguinardo.cat/gestioalumnes/students/alta-inici',
        ['class' => 'app-topbar__cta', 'target' => '_blank', 'rel' => 'noopener']
    ) ?>
</header>

<!-- IMPORTANT: wrapper per a GRID (desktop) -->
<div class="app-shell">

    <!-- SIDEBAR -->
    <aside class="app-sidebar" id="appSidebar" aria-hidden="true">
        <button class="app-sidebar__close" id="closeSidebarBtn" type="button" aria-label="Tancar menú">✕</button>

        <div class="app-sidebar__content">
                    <?php
        $colors = ['blaumari', 'blaucel', 'verd', 'rosa', 'lila', 'taronja', 'gris', 'ocre'];
        $i = 0;
        ?>

        <?php foreach ($pages as $p): ?>
            <?php
                $level = $pageLevel((string)$p->order_code);
                $indentRem = max(0, $level - 1);

                // color per seqüència (cíclic)
                $color = $colors[$i % count($colors)];
                $i++;
            ?>

            <div class="sidebar-item" style="margin-left: <?= h($indentRem) ?>rem;">
                <?= $this->element('bototext', [
                    'text'  => $p->title,
                    'image' => null, // <-- IMPORTANT: sense imatge
                    'link'  => ['controller' => 'Pagines', 'action' => 'view', $p->id],
                    'title' => $p->title,
                    'color' => $color,
                    'class' => 'btn-page' // classe extra per estil “pàgines”
                ]) ?>
            </div>
        <?php endforeach; ?>
        </div>
    </aside>

    <!-- CONTINGUT -->
    <main class="app-main">
        <div class="app-container">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>

</div><!-- /.app-shell -->

<!-- BACKDROP (mòbil) -->
<div class="app-backdrop" id="appBackdrop" hidden></div>

<footer></footer>

<!-- JS -->
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

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && document.body.classList.contains('sidebar-open')) {
            closeSidebar();
        }
    });
})();
</script>

</body>
</html>
