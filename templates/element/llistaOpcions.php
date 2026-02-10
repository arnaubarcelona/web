<?php
use Cake\ORM\TableRegistry;
use Cake\Collection\CollectionInterface;

/**
 * Element: llistaOpcions
 *
 * Paràmetres:
 * - table, idField, labelField, name, type, class
 * - entity, field, selected (opc.)
 * - orderField, orderDir (opc.)
 * - records (opc.)  // si vols passar tu mateix id=>label
 */

$table      = $table      ?? null;
$idField    = $idField    ?? 'id';
$labelField = $labelField ?? 'name';
$name       = $name       ?? 'field';
$type       = $type       ?? 'radio';
$class      = $class      ?? '';

$field      = $field      ?? $name;
$entity     = $entity     ?? null;

$orderField = $orderField ?? $labelField;
$orderDir   = strtoupper($orderDir ?? 'ASC');
$orderDir   = in_array($orderDir, ['ASC','DESC'], true) ? $orderDir : 'ASC';

$records    = $records ?? null; // opcional: array id=>label

$itemClass = $itemClass ?? 'llistaopcions-item';
$labelClass = $labelClass ?? '';    // classe extra pel span del text


// --- 1) Determinar seleccionats ------------------------------------
$selected = $selected ?? null;

if ($selected === null) {
    $posted = $this->getRequest()->getData($name);
    if ($posted !== null) $selected = $posted;
}

if ($selected === null && $entity) {
    $selected = $entity->get($field);
}

if ($selected === null) {
    $selected = [];
} elseif ($type === 'checkbox') {
    if ($selected instanceof CollectionInterface) {
        $selected = $selected->map(function ($item) {
            return (is_object($item) && isset($item->id)) ? $item->id : $item;
        })->toList();
    } elseif (is_array($selected)) {
        $selected = array_map(function ($item) {
            if (is_object($item) && isset($item->id)) return $item->id;
            if (is_array($item) && isset($item['id'])) return $item['id'];
            return $item;
        }, $selected);
    } else {
        $selected = [$selected];
    }
} else {
    if (!is_array($selected)) $selected = [$selected];
}

$selected = array_map('strval', $selected);

// --- 2) Preparar el name HTML (notació amb corxets) ----------------
$htmlName = $name;

if (strpos($name, '.') !== false) {
    [$first, $rest] = explode('.', $name, 2);
    $htmlName = $first . '[' . $rest . ']';
}
if ($type === 'checkbox') {
    $htmlName .= '[]';
}

// --- 3) Carregar registres ----------------------------------------
if ($records === null) {
    if (!is_string($table) || $table === '') {
        throw new \InvalidArgumentException("llistaOpcions: falta passar 'table' o bé 'records'.");
    }

    $tableInstance = TableRegistry::getTableLocator()->get($table);

    $records = $tableInstance->find()
        ->select([
            $idField,
            'name_ca','name_es','name_en','name_fr','name_ar','name_ru','name_zh','name_ne',
            $labelField // fallback
        ])
        ->order([$orderField => $orderDir])
        ->all();
}
?>

<div class="<?= h($class) ?>">
<?php foreach ($records as $k => $r): ?>

    <?php
    // ✅ Cas A: id=>label (array/list) => $r és string
    if (!is_object($r)) {
        $valueStr = (string)$k;
        $labelDefault = (string)$r;
        $d_ca = $labelDefault; $d_es=''; $d_en=''; $d_fr=''; $d_ar=''; $d_ru=''; $d_zh=''; $d_ne='';
    } else {
        // ✅ Cas B: entitat => tenim name_ca..name_ne
        $valueStr = (string)($r->{$idField} ?? '');
        $labelDefault = (string)($r->name_ca ?? ($r->{$labelField} ?? ''));

        $d_ca = (string)($r->name_ca ?? '');
        $d_es = (string)($r->name_es ?? '');
        $d_en = (string)($r->name_en ?? '');
        $d_fr = (string)($r->name_fr ?? '');
        $d_ar = (string)($r->name_ar ?? '');
        $d_ru = (string)($r->name_ru ?? '');
        $d_zh = (string)($r->name_zh ?? '');
        $d_ne = (string)($r->name_ne ?? '');
    }

    $inputId   = $name . '_' . $valueStr;
    $isChecked = in_array($valueStr, $selected, true);
    ?>

    <?php if ($type === 'checkbox'): ?>
        <label for="<?= h($inputId) ?>" class="<?= h($itemClass) ?>">
            <input
                type="checkbox"
                id="<?= h($inputId) ?>"
                name="<?= h($htmlName) ?>"
                value="<?= h($valueStr) ?>"
                <?= $isChecked ? 'checked' : '' ?>
            >
            <span
                class="llistaopcions-label"
                data-ca="<?= h($d_ca) ?>"
                data-es="<?= h($d_es) ?>"
                data-en="<?= h($d_en) ?>"
                data-fr="<?= h($d_fr) ?>"
                data-ar="<?= h($d_ar) ?>"
                data-ru="<?= h($d_ru) ?>"
                data-zh="<?= h($d_zh) ?>"
                data-ne="<?= h($d_ne) ?>"
            ><?= h($labelDefault) ?></span>
        </label>
    <?php else: ?>
      <label for="<?= h($inputId) ?>" class="llistaopcions-item <?= h($itemClass) ?>">
        <input
          type="radio"
          id="<?= h($inputId) ?>"
          name="<?= h($htmlName) ?>"
          value="<?= h($valueStr) ?>"
          <?= $isChecked ? 'checked' : '' ?>
        >
        <span
          class="llistaopcions-label <?= h($labelClass) ?>"
          data-ca="<?= h($d_ca) ?>"
          data-es="<?= h($d_es) ?>"
          data-en="<?= h($d_en) ?>"
          data-fr="<?= h($d_fr) ?>"
          data-ar="<?= h($d_ar) ?>"
          data-ru="<?= h($d_ru) ?>"
          data-zh="<?= h($d_zh) ?>"
          data-ne="<?= h($d_ne) ?>"
        ><?= h($labelDefault) ?></span>
      </label>
    <?php endif; ?>



<?php endforeach; ?>
</div>
