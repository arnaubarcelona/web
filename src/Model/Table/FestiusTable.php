<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Festius Model
 *
 * @method \App\Model\Entity\Festius newEmptyEntity()
 * @method \App\Model\Entity\Festius newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Festius[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Festius get($primaryKey, $options = [])
 * @method \App\Model\Entity\Festius findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Festius patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Festius[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Festius|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Festius saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Festius[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Festius[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Festius[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Festius[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class FestiusTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('festius');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->date('data')
            ->requirePresence('data', 'create')
            ->notEmptyDate('data');

        return $validator;
    }
}
