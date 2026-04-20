<?php
/**
 * Galeria automàtica d'imatges a partir de webroot/uploads/galeriaN.ext
 * Ús: {galeria}
 *
 * @var \App\View\AppView $this
 */

declare(strict_types=1);

$uploadsPath = ROOT . DS . 'webroot' . DS . 'uploads';
$images = [];

if (is_dir($uploadsPath)) {
    $entries = scandir($uploadsPath) ?: [];

    foreach ($entries as $entry) {
        if (!preg_match('/^galeria(\d+)\.(jpe?g|png|gif|webp|avif)$/i', $entry, $matches)) {
            continue;
        }

        $images[] = [
            'file' => $entry,
            'order' => (int)$matches[1],
        ];
    }
}

usort($images, function (array $a, array $b): int {
    $orderCompare = $a['order'] <=> $b['order'];
    if ($orderCompare !== 0) {
        return $orderCompare;
    }

    return strnatcasecmp($a['file'], $b['file']);
});

$carouselId = 'galeria-carousel-' . str_replace('.', '', uniqid('', true));
?>

<?php if ($images !== []): ?>
    <div id="<?= h($carouselId) ?>" class="galeria-carousel" aria-label="Galeria d'imatges del centre">
        <?php foreach ($images as $index => $image): ?>
            <?php $imageUrl = $this->Url->build('/uploads/' . rawurlencode($image['file'])); ?>
            <img
                src="<?= h($imageUrl) ?>"
                alt="Imatge de la galeria <?= $index + 1 ?>"
                class="galeria-carousel__image<?= $index === 0 ? ' is-active' : '' ?>"
                loading="lazy"
                decoding="async"
            >
        <?php endforeach; ?>
    </div>

    <style>
    #<?= h($carouselId) ?> {
        position: relative;
        width: min(100%, 30rem);
        height: min(30rem, 75vw);
        max-width: 30rem;
        max-height: 30rem;
        margin: 0 auto;
        overflow: hidden;
    }

    #<?= h($carouselId) ?> .galeria-carousel__image {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        opacity: 0;
        transition: opacity 800ms ease-in-out;
        pointer-events: none;
        background: transparent;
    }

    #<?= h($carouselId) ?> .galeria-carousel__image.is-active {
        opacity: 1;
    }
    </style>

    <script>
    (function () {
        const carousel = document.getElementById('<?= h($carouselId) ?>');
        if (!carousel) {
            return;
        }

        const images = Array.from(carousel.querySelectorAll('.galeria-carousel__image'));
        if (images.length <= 1) {
            return;
        }

        let currentIndex = 0;

        window.setInterval(function () {
            const nextIndex = (currentIndex + 1) % images.length;
            images[currentIndex].classList.remove('is-active');
            images[nextIndex].classList.add('is-active');
            currentIndex = nextIndex;
        }, 3000);
    })();
    </script>
<?php endif; ?>
