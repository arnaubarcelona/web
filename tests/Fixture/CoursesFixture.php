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
                'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
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
                'datainici' => '2026-02-10',
                'datafi' => '2026-02-10',
                'horesanuals' => 1,
                'created' => 1770723141,
                'modified' => 1770723141,
                'competenciatic_id' => 1,
            ],
        ];
        parent::init();
    }
}
