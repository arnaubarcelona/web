<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SortidatornsCoursesFixture
 */
class SortidatornsCoursesFixture extends TestFixture
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
                'sortidatorn_id' => 1,
                'course_id' => 1,
            ],
        ];
        parent::init();
    }
}
