<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Sortide Entity
 *
 * @property int $id
 * @property int|null $tipuspagamentsortida_id
 * @property string|null $titol
 * @property string|null $lloc
 * @property string|null $adreca
 * @property string|null $nompunttrobada
 * @property string|null $urlmapslloc
 * @property string|null $urlmapstrobada
 * @property string|null $observacions
 * @property string|null $url
 *
 * @property \App\Model\Entity\Sortidatorn[] $sortidatorns
 */
class Sortide extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'tipuspagamentsortida_id' => true,
        'titol' => true,
        'lloc' => true,
        'adreca' => true,
        'nompunttrobada' => true,
        'urlmapslloc' => true,
        'urlmapstrobada' => true,
        'observacions' => true,
        'url' => true,
        'sortidatorns' => true,
    ];
}
