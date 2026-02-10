<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Horari Entity
 *
 * @property int $id
 * @property int $course_id
 * @property int $day_id
 * @property \Cake\I18n\Time $horainici
 * @property \Cake\I18n\Time $horafinal
 * @property string $durada
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Course $course
 * @property \App\Model\Entity\Day $day
 */
class Horari extends Entity
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
        'course_id' => true,
        'day_id' => true,
        'horainici' => true,
        'horafinal' => true,
        'durada' => true,
        'created' => true,
        'modified' => true,
        'course' => true,
        'day' => true,
    ];
}
