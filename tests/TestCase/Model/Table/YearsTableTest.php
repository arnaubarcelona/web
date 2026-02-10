<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\YearsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\YearsTable Test Case
 */
class YearsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\YearsTable
     */
    protected $Years;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Years',
        'app.Courses',
        'app.Sortidatorns',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Years') ? [] : ['className' => YearsTable::class];
        $this->Years = $this->getTableLocator()->get('Years', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Years);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\YearsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
