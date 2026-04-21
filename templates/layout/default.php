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
    $orderA = trim((string)$a->order_code);
    $orderB = trim((string)$b->order_code);

    $emptyA = ($orderA === '');
    $emptyB = ($orderB === '');

    if ($emptyA && !$emptyB) {
        return 1;
    }
    if (!$emptyA && $emptyB) {
        return -1;
    }
    if ($emptyA && $emptyB) {
        return 0;
    }

    $pa = array_map('intval', explode('.', $orderA));
    $pb = array_map('intval', explode('.', $orderB));

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

$appMainClass = trim((string)$this->fetch('appMainClass'));
$isMenuPpalLayout = str_contains($appMainClass, 'has-menuppal');
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($cakeDescription) ?>: <?= $this->fetch('title') ?></title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css(['fonts', 'cake']) ?>
    <?= $this->Html->css('layout_custom') ?>
    <?= $this->Html->css('pestanya') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('calendar') ?>
    <?= $this->fetch('script') ?>
</head>
<body class="<?= $isMenuPpalLayout ? 'is-menuppal-page' : 'is-regular-page' ?>">

<!-- TOPBAR -->
<header class="app-topbar" id="appTopbar">

    <!-- Branding TOPBAR (NOMÉS MÒBIL) -->
    <div class="topbar-brand topbar-brand--mobile">
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

            <!-- BOTÓ MENU (sidebar) - NOMÉS MÒBIL -->
            <button
                class="app-topbar__menuImgBtn"
                id="openSidebarBtn"
                aria-label="Obrir menú"
                type="button"
            >MENU</button>

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

    <!-- Branding TOPBAR (NOMÉS DESKTOP en pàgines amb menuppal) -->
    <div class="topbar-brand topbar-brand--desktop-menuppal">
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

    <!-- CTA (NOMÉS DESKTOP) -->
    <?= $this->Html->link(
        'INSCRIU-TE',
        'http://www.cfaguinardo.cat/gestioalumnes/students/alta-inici',
        ['class' => 'app-topbar__cta', 'target' => '_blank', 'rel' => 'noopener']
    ) ?>
</header>

<!-- SHELL (DESKTOP: SIDEBAR + AREA DRETA) -->
<div class="app-shell">

    <!-- SIDEBAR -->
    <aside class="app-sidebar" id="appSidebar" aria-hidden="true">
        <!-- Tancar (NOMÉS MÒBIL) -->
        <button class="app-sidebar__close" id="closeSidebarBtn" type="button" aria-label="Tancar menú">✕</button>

        <!-- Branding SIDEBAR (NOMÉS DESKTOP) -->
        <div class="sidebar-brand sidebar-brand--desktop">
            <?= $this->Html->image('logoGran.png', [
                'alt' => 'CFA Guinardó',
                'class' => 'sidebar-logo'
            ]) ?>

            <div class="sidebar-social">
                <?= $this->Html->link(
                    $this->Html->image('instagram.png', [
                        'alt' => 'Instagram',
                        'class' => 'sidebar-socialIcon'
                    ]),
                    'https://www.instagram.com/cfaguinardo',
                    ['escape' => false, 'target' => '_blank', 'rel' => 'noopener']
                ) ?>

                <?= $this->Html->link(
                    $this->Html->image('facebook.png', [
                        'alt' => 'Facebook',
                        'class' => 'sidebar-socialIcon'
                    ]),
                    'https://www.facebook.com/people/Cfa-Guinardo/61560117734842/',
                    ['escape' => false, 'target' => '_blank', 'rel' => 'noopener']
                ) ?>
            </div>
        </div>

        <!-- Botons / Pàgines -->
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
                        'image' => null,
                        'link'  => ['controller' => 'Pagines', 'action' => 'view', $p->id],
                        'title' => $p->title,
                        'color' => $color,
                        'class' => 'btn-page'
                    ]) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </aside>

    <!-- CONTINGUT -->
    <main class="app-main <?= h($appMainClass) ?>">
        <div class="app-container">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>

</div><!-- /.app-shell -->

<!-- BACKDROP (MÒBIL) -->
<div class="app-backdrop" id="appBackdrop" hidden></div>

<!-- BOTTOMBAR -->
<footer class="app-bottombar">
    <div class="app-bottombar__inner">
        <section class="app-bottombar__col app-bottombar__col--left">
            <div class="bottombar-logos">
                <?= $this->Html->image('line_small.png', [
                    'alt' => 'Línia corporativa',
                    'class' => 'bottombar-logos__line'
                ]) ?>
                <?= $this->Html->image('consorci.jpg', [
                    'alt' => 'Consorci d’Educació de Barcelona',
                    'class' => 'bottombar-logos__consorci'
                ]) ?>
            </div>
        </section>

        <section class="app-bottombar__col">
            <div class="bottombar-title">CONTACTE</div>
            <div class="bottombar-text">
                <?= $this->element('contactecentre') ?>
            </div>
        </section>

        <section class="app-bottombar__col app-bottombar__col--right">
            <div class="bottombar-title">HORARI D’ATENCIÓ</div>
            <div class="bottombar-text">
                <?= $this->element('horarisatencio') ?>
            </div>
        </section>
    </div>
</footer>

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
