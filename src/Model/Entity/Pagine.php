<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Pagine Entity
 *
 * @property int $id
 * @property string $title
 * @property string $body
 * @property string $order_code
 * @property bool $visible
 * @property bool $main
 * @property string|null $description
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 */
class Pagine extends Entity
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
        'title' => true,
        'body' => true,
        'order_code' => true,
        'visible' => true,
        'main' => true,
        'description' => true,
        'created' => true,
        'modified' => true,
        'link' => true,
        'popup' => true,
        'embed' => true,
    ];
}
