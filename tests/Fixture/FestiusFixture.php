<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FestiusFixture
 */
class FestiusFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'festius';
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
                'data' => '2026-02-10',
            ],
        ];
        parent::init();
    }
}
