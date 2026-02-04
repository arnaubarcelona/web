<?php
/**
 * @var \App\View\AppView $this
 * @var array<int, array<string, mixed>> $months
 * @var string $courseLabel
 * @var \Cake\I18n\FrozenDate $datainici
 * @var \Cake\I18n\FrozenDate $datafi
 */
?>
<?php $this->assign('title', __('Calendari')); ?>
<?= $this->Html->css('calendar', ['block' => true]) ?>

<section class="annual-calendar">
    <aside class="annual-calendar__intro">
        <div class="annual-calendar__logo">
            <span class="annual-calendar__logo-mark">CFA</span>
            <span class="annual-calendar__logo-name">GUINARDÓ</span>
            <span class="annual-calendar__logo-subtitle">Centre de Formació d'Adults</span>
        </div>
        <div class="annual-calendar__course">
            <?= h($courseLabel) ?>
        </div>
        <div class="annual-calendar__legend">
            <div class="annual-calendar__legend-item">
                <span class="annual-calendar__legend-swatch calendar-day--lectiu"></span>
                <span><?= __('Dies lectius') ?></span>
            </div>
            <div class="annual-calendar__legend-item">
                <span class="annual-calendar__legend-swatch calendar-day--obert"></span>
                <span><?= __('Centre obert (no lectiu)') ?></span>
            </div>
            <div class="annual-calendar__legend-item">
                <span class="annual-calendar__legend-swatch calendar-day--festiu"></span>
                <span><?= __('Festius i caps de setmana') ?></span>
            </div>
        </div>
    </aside>

    <div class="annual-calendar__months">
        <?php foreach ($months as $month): ?>
            <div class="month-card">
                <div class="month-card__header"><?= h($month['label']) ?></div>
                <table class="month-card__table">
                    <thead>
                        <tr>
                            <th>dl</th>
                            <th>dt</th>
                            <th>dc</th>
                            <th>dj</th>
                            <th>dv</th>
                            <th>ds</th>
                            <th>dg</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($month['weeks'] as $week): ?>
                            <tr>
                                <?php foreach ($week as $day): ?>
                                    <?php if ($day === null): ?>
                                        <td class="month-card__empty"></td>
                                    <?php else: ?>
                                        <td class="<?= h($day['class']) ?>">
                                            <?= h($day['number']) ?>
                                        </td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>
</section>
