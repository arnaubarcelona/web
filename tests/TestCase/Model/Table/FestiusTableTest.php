<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FestiusTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FestiusTable Test Case
 */
class FestiusTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FestiusTable
     */
    protected $Festius;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Festius',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Festius') ? [] : ['className' => FestiusTable::class];
        $this->Festius = $this->getTableLocator()->get('Festius', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Festius);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\FestiusTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
