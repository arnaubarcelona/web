<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Course Entity
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property int $propi
 * @property int|null $parentcourse_id
 * @property bool $microgrup
 * @property int $subject_id
 * @property int $level
 * @property int $mode_id
 * @property int|null $trimestre
 * @property int|null $quadrimestre
 * @property int $year_id
 * @property int $size
 * @property int $torn_id
 * @property int $aula_id
 * @property \Cake\I18n\FrozenDate $datainici
 * @property \Cake\I18n\FrozenDate $datafi
 * @property int|null $horesanuals
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $competenciatic_id
 *
 * @property \App\Model\Entity\Subject $subject
 * @property \App\Model\Entity\Mode $mode
 * @property \App\Model\Entity\Year $year
 * @property \App\Model\Entity\Torn $torn
 * @property \App\Model\Entity\Aula $aula
 * @property \App\Model\Entity\Horari[] $horaris
 * @property \App\Model\Entity\Sortidatorn[] $sortidatorns
 */
class Course extends Entity
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
        'code' => true,
        'name' => true,
        'description' => true,
        'propi' => true,
        'parentcourse_id' => true,
        'microgrup' => true,
        'subject_id' => true,
        'level' => true,
        'mode_id' => true,
        'trimestre' => true,
        'quadrimestre' => true,
        'year_id' => true,
        'size' => true,
        'torn_id' => true,
        'aula_id' => true,
        'datainici' => true,
        'datafi' => true,
        'horesanuals' => true,
        'created' => true,
        'modified' => true,
        'competenciatic_id' => true,
        'subject' => true,
        'mode' => true,
        'year' => true,
        'torn' => true,
        'aula' => true,
        'horaris' => true,
        'sortidatorns' => true,
    ];
}
