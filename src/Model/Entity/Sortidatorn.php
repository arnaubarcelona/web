<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Sortidatorn Entity
 *
 * @property int $id
 * @property int|null $sortide_id
 * @property string|null $nom
 * @property \Cake\I18n\FrozenDate|null $data
 * @property \Cake\I18n\Time|null $horatrobada
 * @property \Cake\I18n\Time|null $hora
 * @property \Cake\I18n\Time|null $durada
 * @property int|null $capacitat
 * @property string|null $preu
 * @property bool|null $visitaguiada
 * @property int|null $year_id
 * @property \Cake\I18n\FrozenDate|null $datalimitinscripcio
 * @property \Cake\I18n\FrozenDate|null $datalimitpagament
 *
 * @property \App\Model\Entity\Sortide $sortide
 * @property \App\Model\Entity\Year $year
 * @property \App\Model\Entity\Course[] $courses
 */
class Sortidatorn extends Entity
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
        'sortide_id' => true,
        'nom' => true,
        'data' => true,
        'horatrobada' => true,
        'hora' => true,
        'durada' => true,
        'capacitat' => true,
        'preu' => true,
        'visitaguiada' => true,
        'year_id' => true,
        'datalimitinscripcio' => true,
        'datalimitpagament' => true,
        'sortide' => true,
        'year' => true,
        'courses' => true,
    ];
}
