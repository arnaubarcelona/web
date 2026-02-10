<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TornsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TornsTable Test Case
 */
class TornsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TornsTable
     */
    protected $Torns;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Torns',
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
        $config = $this->getTableLocator()->exists('Torns') ? [] : ['className' => TornsTable::class];
        $this->Torns = $this->getTableLocator()->get('Torns', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Torns);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\TornsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
