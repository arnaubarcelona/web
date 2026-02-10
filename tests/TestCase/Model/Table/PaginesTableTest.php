<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PaginesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PaginesTable Test Case
 */
class PaginesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PaginesTable
     */
    protected $Pagines;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Pagines',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Pagines') ? [] : ['className' => PaginesTable::class];
        $this->Pagines = $this->getTableLocator()->get('Pagines', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Pagines);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\PaginesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
