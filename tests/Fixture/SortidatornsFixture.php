<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SortidatornsFixture
 */
class SortidatornsFixture extends TestFixture
{
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
                'sortide_id' => 1,
                'nom' => 'Lorem ipsum dolor sit amet',
                'data' => '2026-02-10',
                'horatrobada' => '11:32:22',
                'hora' => '11:32:22',
                'durada' => '11:32:22',
                'capacitat' => 1,
                'preu' => 1.5,
                'visitaguiada' => 1,
                'year_id' => 1,
                'datalimitinscripcio' => '2026-02-10',
                'datalimitpagament' => '2026-02-10',
            ],
        ];
        parent::init();
    }
}
