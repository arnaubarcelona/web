<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CoursesFixture
 */
class CoursesFixture extends TestFixture
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
                'code' => 'Lorem ip',
                'name' => 'Lorem ipsum dolor sit amet',
                'propi' => 1,
                'parentcourse_id' => 1,
                'microgrup' => 1,
                'subject_id' => 1,
                'level' => 1,
                'mode_id' => 1,
                'trimestre' => 1,
                'quadrimestre' => 1,
                'year_id' => 1,
                'size' => 1,
                'torn_id' => 1,
                'aula_id' => 1,
                'datainici' => '2026-02-04',
                'datafi' => '2026-02-04',
                'horesanuals' => 1,
                'created' => 1770222875,
                'modified' => 1770222875,
                'competenciatic_id' => 1,
            ],
        ];
        parent::init();
    }
}
