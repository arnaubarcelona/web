<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * HorarisatencioFixture
 */
class HorarisatencioFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'horarisatencio';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'day_id' => 1,
                'specialdate' => '2026-02-10',
                'horainici' => '11:32:22',
                'horafinal' => '11:32:22',
                'created' => 1770723142,
                'modified' => 1770723142,
            ],
        ];
        parent::init();
    }
}
