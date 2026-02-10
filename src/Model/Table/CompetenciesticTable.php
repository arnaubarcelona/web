<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Competenciestic Model
 *
 * @method \App\Model\Entity\Competenciestic newEmptyEntity()
 * @method \App\Model\Entity\Competenciestic newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Competenciestic[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Competenciestic get($primaryKey, $options = [])
 * @method \App\Model\Entity\Competenciestic findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Competenciestic patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Competenciestic[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Competenciestic|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Competenciestic saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Competenciestic[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Competenciestic[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Competenciestic[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Competenciestic[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class CompetenciesticTable extends Table
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

        $this->setTable('competenciestic');
        $this->setDisplayField('codi');
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
            ->scalar('codi')
            ->maxLength('codi', 50)
            ->requirePresence('codi', 'create')
            ->notEmptyString('codi');

        $validator
            ->scalar('nom')
            ->maxLength('nom', 255)
            ->requirePresence('nom', 'create')
            ->notEmptyString('nom');

        return $validator;
    }
}
