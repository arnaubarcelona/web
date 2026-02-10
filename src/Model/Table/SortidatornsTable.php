<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Sortidatorns Model
 *
 * @property \App\Model\Table\SortidesTable&\Cake\ORM\Association\BelongsTo $Sortides
 * @property \App\Model\Table\YearsTable&\Cake\ORM\Association\BelongsTo $Years
 * @property \App\Model\Table\CoursesTable&\Cake\ORM\Association\BelongsToMany $Courses
 *
 * @method \App\Model\Entity\Sortidatorn newEmptyEntity()
 * @method \App\Model\Entity\Sortidatorn newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Sortidatorn[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Sortidatorn get($primaryKey, $options = [])
 * @method \App\Model\Entity\Sortidatorn findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Sortidatorn patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Sortidatorn[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Sortidatorn|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Sortidatorn saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Sortidatorn[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Sortidatorn[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Sortidatorn[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Sortidatorn[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SortidatornsTable extends Table
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

        $this->setTable('sortidatorns');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Sortides', [
            'foreignKey' => 'sortide_id',
        ]);
        $this->belongsTo('Years', [
            'foreignKey' => 'year_id',
        ]);
        $this->belongsToMany('Courses', [
            'foreignKey' => 'sortidatorn_id',
            'targetForeignKey' => 'course_id',
            'joinTable' => 'sortidatorns_courses',
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
            ->integer('sortide_id')
            ->allowEmptyString('sortide_id');

        $validator
            ->scalar('nom')
            ->maxLength('nom', 100)
            ->allowEmptyString('nom');

        $validator
            ->date('data')
            ->allowEmptyDate('data');

        $validator
            ->time('horatrobada')
            ->allowEmptyTime('horatrobada');

        $validator
            ->time('hora')
            ->allowEmptyTime('hora');

        $validator
            ->time('durada')
            ->allowEmptyTime('durada');

        $validator
            ->integer('capacitat')
            ->allowEmptyString('capacitat');

        $validator
            ->decimal('preu')
            ->allowEmptyString('preu');

        $validator
            ->boolean('visitaguiada')
            ->allowEmptyString('visitaguiada');

        $validator
            ->integer('year_id')
            ->allowEmptyString('year_id');

        $validator
            ->date('datalimitinscripcio')
            ->allowEmptyDate('datalimitinscripcio');

        $validator
            ->date('datalimitpagament')
            ->allowEmptyDate('datalimitpagament');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('sortide_id', 'Sortides'), ['errorField' => 'sortide_id']);
        $rules->add($rules->existsIn('year_id', 'Years'), ['errorField' => 'year_id']);

        return $rules;
    }
}
