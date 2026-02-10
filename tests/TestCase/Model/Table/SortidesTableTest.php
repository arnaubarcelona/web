<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SortidesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SortidesTable Test Case
 */
class SortidesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SortidesTable
     */
    protected $Sortides;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Sortides',
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
        $config = $this->getTableLocator()->exists('Sortides') ? [] : ['className' => SortidesTable::class];
        $this->Sortides = $this->getTableLocator()->get('Sortides', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Sortides);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SortidesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
