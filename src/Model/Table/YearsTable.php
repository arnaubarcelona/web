<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Years Model
 *
 * @property \App\Model\Table\CoursesTable&\Cake\ORM\Association\HasMany $Courses
 * @property \App\Model\Table\SortidatornsTable&\Cake\ORM\Association\HasMany $Sortidatorns
 *
 * @method \App\Model\Entity\Year newEmptyEntity()
 * @method \App\Model\Entity\Year newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Year[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Year get($primaryKey, $options = [])
 * @method \App\Model\Entity\Year findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Year patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Year[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Year|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Year saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Year[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Year[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Year[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Year[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class YearsTable extends Table
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

        $this->setTable('years');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Courses', [
            'foreignKey' => 'year_id',
        ]);
        $this->hasMany('Sortidatorns', [
            'foreignKey' => 'year_id',
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
            ->maxLength('name', 10)
            ->allowEmptyString('name');

        $validator
            ->date('datainicipreinscripcio')
            ->allowEmptyDate('datainicipreinscripcio');

        $validator
            ->date('datafipreinscripcio')
            ->allowEmptyDate('datafipreinscripcio');

        $validator
            ->date('databaremprovisional')
            ->allowEmptyDate('databaremprovisional');

        $validator
            ->date('datainicireclamacions')
            ->allowEmptyDate('datainicireclamacions');

        $validator
            ->date('datafireclamacions')
            ->allowEmptyDate('datafireclamacions');

        $validator
            ->date('datallistaadmesos')
            ->allowEmptyDate('datallistaadmesos');

        $validator
            ->date('datainici')
            ->allowEmptyDate('datainici');

        $validator
            ->date('datainiciconfirmaciocontinuitat')
            ->allowEmptyDate('datainiciconfirmaciocontinuitat');

        $validator
            ->date('dataficonfirmaciocontinuitat')
            ->allowEmptyDate('dataficonfirmaciocontinuitat');

        $validator
            ->date('datainicimatricula')
            ->allowEmptyDate('datainicimatricula');

        $validator
            ->date('datafimatricula')
            ->allowEmptyDate('datafimatricula');

        $validator
            ->date('datainiciabandonament')
            ->allowEmptyDate('datainiciabandonament');

        $validator
            ->integer('diescaducitatllistaespera')
            ->allowEmptyString('diescaducitatllistaespera');

        $validator
            ->integer('diesnojustificades')
            ->allowEmptyString('diesnojustificades');

        $validator
            ->date('datafi')
            ->allowEmptyDate('datafi');

        return $validator;
    }
}
