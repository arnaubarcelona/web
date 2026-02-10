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
                'datainicipreinscripcio' => '2026-02-04',
                'datafipreinscripcio' => '2026-02-04',
                'databaremprovisional' => '2026-02-04',
                'datainicireclamacions' => '2026-02-04',
                'datafireclamacions' => '2026-02-04',
                'datallistaadmesos' => '2026-02-04',
                'datainici' => '2026-02-04',
                'datainiciconfirmaciocontinuitat' => '2026-02-04',
                'dataficonfirmaciocontinuitat' => '2026-02-04',
                'datainicimatricula' => '2026-02-04',
                'datafimatricula' => '2026-02-04',
                'datainiciabandonament' => '2026-02-04',
                'diescaducitatllistaespera' => 1,
                'diesnojustificades' => 1,
                'datafi' => '2026-02-04',
                'created' => 1770222876,
                'modified' => 1770222876,
            ],
        ];
        parent::init();
    }
}
