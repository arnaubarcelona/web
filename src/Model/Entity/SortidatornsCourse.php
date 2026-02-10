<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SortidatornsCourse Entity
 *
 * @property int $id
 * @property int|null $sortidatorn_id
 * @property int|null $course_id
 *
 * @property \App\Model\Entity\Sortidatorn $sortidatorn
 * @property \App\Model\Entity\Course $course
 */
class SortidatornsCourse extends Entity
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
        'sortidatorn_id' => true,
        'course_id' => true,
        'sortidatorn' => true,
        'course' => true,
    ];
}
