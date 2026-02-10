<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ConfigsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ConfigsTable Test Case
 */
class ConfigsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ConfigsTable
     */
    protected $Configs;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Configs',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Configs') ? [] : ['className' => ConfigsTable::class];
        $this->Configs = $this->getTableLocator()->get('Configs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Configs);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ConfigsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
