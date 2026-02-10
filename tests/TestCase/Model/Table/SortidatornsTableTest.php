<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SortidatornsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SortidatornsTable Test Case
 */
class SortidatornsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SortidatornsTable
     */
    protected $Sortidatorns;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Sortidatorns',
        'app.Sortides',
        'app.Years',
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
        $config = $this->getTableLocator()->exists('Sortidatorns') ? [] : ['className' => SortidatornsTable::class];
        $this->Sortidatorns = $this->getTableLocator()->get('Sortidatorns', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Sortidatorns);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SortidatornsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\SortidatornsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
