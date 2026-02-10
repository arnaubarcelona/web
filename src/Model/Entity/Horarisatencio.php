<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Horarisatencio Entity
 *
 * @property int $id
 * @property int|null $day_id
 * @property \Cake\I18n\FrozenDate|null $specialdate
 * @property \Cake\I18n\Time $horainici
 * @property \Cake\I18n\Time $horafinal
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Day $day
 */
class Horarisatencio extends Entity
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
        'day_id' => true,
        'specialdate' => true,
        'horainici' => true,
        'horafinal' => true,
        'created' => true,
        'modified' => true,
        'day' => true,
    ];
}
