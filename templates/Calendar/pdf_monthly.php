<?php
/** @var \App\View\AppView $this */
/** @var array<int, array{label:string,weeks:array<int,array<int,array{number:int,class:string}|null>>}> $months */
/** @var string $courseLabel */
?>
<!doctype html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($courseLabel) ?> - PDF mensual</title>
    <?= $this->Html->css('calendar_print') ?>
</head>
<body>
    <div class="calendar-print__months--monthly">
        <?php foreach ($months as $month): ?>
            <section class="pdf-month-page">
                <header class="calendar-print__header">
                    <h1 class="calendar-print__title"><?= h($courseLabel) ?> · <?= h($month['label']) ?></h1>
                </header>

                <div class="calendar-print__legend">
                    <div class="calendar-print__legend-item"><span class="calendar-print__swatch calendar-day--lectiu"></span><span><?= __('Obert (lectiu)') ?></span></div>
                    <div class="calendar-print__legend-item"><span class="calendar-print__swatch calendar-day--obert"></span><span><?= __('Obert (no lectiu)') ?></span></div>
                    <div class="calendar-print__legend-item"><span class="calendar-print__swatch calendar-day--festiu"></span><span><?= __('Festiu') ?></span></div>
                    <div class="calendar-print__legend-item"><span class="calendar-print__swatch calendar-day--closed"></span><span><?= __('Tancat') ?></span></div>
                </div>

                <section class="month-card">
                    <div class="month-card__header"><?= h($month['label']) ?></div>
                    <table class="month-card__table">
                        <thead>
                        <tr>
                            <th>dl</th><th>dt</th><th>dc</th><th>dj</th><th>dv</th><th>ds</th><th>dg</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($month['weeks'] as $week): ?>
                            <tr>
                                <?php foreach ($week as $day): ?>
                                    <?php if ($day === null): ?>
                                        <td class="month-card__empty"></td>
                                    <?php else: ?>
                                        <td class="<?= h($day['class']) ?>"><?= h($day['number']) ?></td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
            </section>
        <?php endforeach; ?>
    </div>

    <p class="no-print"><?= __('Consell: fes servir la impressió del navegador i selecciona "Desa com a PDF".') ?></p>
</body>
</html>
