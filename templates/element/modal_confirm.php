<?php
/**
 * Element: modal_confirm
 *
 * @var string $id
 * @var string $message
 * @var array  $buttons
 * @var string|null $containerClass  Classes extra pel div principal (message alert ...)
 * @var string|null $boxClass        Classes extra per la caixa (message-box ...)
 */
?>

<?php
  $containerClass = $containerClass ?? '';
  $boxClass = $boxClass ?? '';
?>

<div id="<?= h($id) ?>" class="message hidden <?= h($containerClass) ?>">
  <div class="message-box <?= h($boxClass) ?>" onclick="event.stopPropagation()">
    <div id="<?= h($id) ?>-text">
      <?= $message ?>
    </div>

    <div class="contenidor divcentre separacio">
      <?php foreach ($buttons as $btn): ?>
        <?php
          $rawOnclick = $btn['onclick'] ?? '';
          // JS “blindat”
          $js = "event.preventDefault(); event.stopPropagation(); {$rawOnclick}; return false;";
          $jsAttr = htmlspecialchars($js, ENT_QUOTES, 'UTF-8');

          $jsKey = "if(event.key==='Enter'||event.key===' '){ event.preventDefault(); event.stopPropagation(); {$rawOnclick}; return false; }";
          $jsKeyAttr = htmlspecialchars($jsKey, ENT_QUOTES, 'UTF-8');
        ?>

        <div
          class="botomsg <?= h($btn['class'] ?? '') ?> augmenta-hover"
          role="button"
          tabindex="0"
          onclick="<?= $jsAttr ?>"
          onkeydown="<?= $jsKeyAttr ?>"
        >
          <?= $btn['text'] ?? '' ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
