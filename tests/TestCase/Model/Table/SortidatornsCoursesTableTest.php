<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SortidatornsCoursesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SortidatornsCoursesTable Test Case
 */
class SortidatornsCoursesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SortidatornsCoursesTable
     */
    protected $SortidatornsCourses;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.SortidatornsCourses',
        'app.Sortidatorns',
        'app.Courses',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('SortidatornsCourses') ? [] : ['className' => SortidatornsCoursesTable::class];
        $this->SortidatornsCourses = $this->getTableLocator()->get('SortidatornsCourses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->SortidatornsCourses);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SortidatornsCoursesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\SortidatornsCoursesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
