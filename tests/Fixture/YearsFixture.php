<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * YearsFixture
 */
class YearsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'name' => 'Lorem ip',
                'datainicipreinscripcio' => '2026-02-10',
                'datafipreinscripcio' => '2026-02-10',
                'databaremprovisional' => '2026-02-10',
                'datainicireclamacions' => '2026-02-10',
                'datafireclamacions' => '2026-02-10',
                'datallistaadmesos' => '2026-02-10',
                'datainici' => '2026-02-10',
                'datainiciconfirmaciocontinuitat' => '2026-02-10',
                'dataficonfirmaciocontinuitat' => '2026-02-10',
                'datainicimatricula' => '2026-02-10',
                'datafimatricula' => '2026-02-10',
                'datainiciabandonament' => '2026-02-10',
                'diescaducitatllistaespera' => 1,
                'diesnojustificades' => 1,
                'datafi' => '2026-02-10',
                'created' => 1770723142,
                'modified' => 1770723142,
            ],
        ];
        parent::init();
    }
}
