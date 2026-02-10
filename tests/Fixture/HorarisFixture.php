<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * HorarisFixture
 */
class HorarisFixture extends TestFixture
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
                'course_id' => 1,
                'day_id' => 1,
                'horainici' => '16:34:35',
                'horafinal' => '16:34:35',
                'durada' => 1.5,
                'created' => 1770222875,
                'modified' => 1770222875,
            ],
        ];
        parent::init();
    }
}
