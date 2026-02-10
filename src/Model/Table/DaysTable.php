<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Days Model
 *
 * @property \App\Model\Table\HorarisTable&\Cake\ORM\Association\HasMany $Horaris
 *
 * @method \App\Model\Entity\Day newEmptyEntity()
 * @method \App\Model\Entity\Day newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Day[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Day get($primaryKey, $options = [])
 * @method \App\Model\Entity\Day findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Day patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Day[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Day|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Day saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Day[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Day[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Day[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Day[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class DaysTable extends Table
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

        $this->setTable('days');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Horaris', [
            'foreignKey' => 'day_id',
        ]);
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
            ->scalar('name')
            ->maxLength('name', 50)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        return $validator;
    }
}
