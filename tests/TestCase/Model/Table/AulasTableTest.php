<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AulasTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AulasTable Test Case
 */
class AulasTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\AulasTable
     */
    protected $Aulas;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Aulas',
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
        $config = $this->getTableLocator()->exists('Aulas') ? [] : ['className' => AulasTable::class];
        $this->Aulas = $this->getTableLocator()->get('Aulas', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Aulas);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\AulasTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
