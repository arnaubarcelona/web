<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DaysTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DaysTable Test Case
 */
class DaysTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\DaysTable
     */
    protected $Days;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Days',
        'app.Horaris',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Days') ? [] : ['className' => DaysTable::class];
        $this->Days = $this->getTableLocator()->get('Days', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Days);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\DaysTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
