<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\HorarisTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\HorarisTable Test Case
 */
class HorarisTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\HorarisTable
     */
    protected $Horaris;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Horaris',
        'app.Courses',
        'app.Days',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Horaris') ? [] : ['className' => HorarisTable::class];
        $this->Horaris = $this->getTableLocator()->get('Horaris', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Horaris);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\HorarisTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\HorarisTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
