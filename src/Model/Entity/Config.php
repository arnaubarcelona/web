<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Config Entity
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $valuetext
 * @property int|null $valueint
 * @property bool|null $valueboolean
 * @property string|null $valuedecimal
 * @property \Cake\I18n\FrozenDate|null $valuedate
 * @property string|null $valuelongtext
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 */
class Config extends Entity
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
        'description' => true,
        'valuetext' => true,
        'valueint' => true,
        'valueboolean' => true,
        'valuedecimal' => true,
        'valuedate' => true,
        'valuelongtext' => true,
        'created' => true,
        'modified' => true,
    ];
}
