<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Year Entity
 *
 * @property int $id
 * @property string|null $name
 * @property \Cake\I18n\FrozenDate|null $datainicipreinscripcio
 * @property \Cake\I18n\FrozenDate|null $datafipreinscripcio
 * @property \Cake\I18n\FrozenDate|null $databaremprovisional
 * @property \Cake\I18n\FrozenDate|null $datainicireclamacions
 * @property \Cake\I18n\FrozenDate|null $datafireclamacions
 * @property \Cake\I18n\FrozenDate|null $datallistaadmesos
 * @property \Cake\I18n\FrozenDate|null $datainici
 * @property \Cake\I18n\FrozenDate|null $datainiciconfirmaciocontinuitat
 * @property \Cake\I18n\FrozenDate|null $dataficonfirmaciocontinuitat
 * @property \Cake\I18n\FrozenDate|null $datainicimatricula
 * @property \Cake\I18n\FrozenDate|null $datafimatricula
 * @property \Cake\I18n\FrozenDate|null $datainiciabandonament
 * @property int|null $diescaducitatllistaespera
 * @property int|null $diesnojustificades
 * @property \Cake\I18n\FrozenDate|null $datafi
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Course[] $courses
 * @property \App\Model\Entity\Sortidatorn[] $sortidatorns
 */
class Year extends Entity
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
        'name' => true,
        'datainicipreinscripcio' => true,
        'datafipreinscripcio' => true,
        'databaremprovisional' => true,
        'datainicireclamacions' => true,
        'datafireclamacions' => true,
        'datallistaadmesos' => true,
        'datainici' => true,
        'datainiciconfirmaciocontinuitat' => true,
        'dataficonfirmaciocontinuitat' => true,
        'datainicimatricula' => true,
        'datafimatricula' => true,
        'datainiciabandonament' => true,
        'diescaducitatllistaespera' => true,
        'diesnojustificades' => true,
        'datafi' => true,
        'created' => true,
        'modified' => true,
        'courses' => true,
        'sortidatorns' => true,
    ];
}
