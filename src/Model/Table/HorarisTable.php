<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Horaris Model
 *
 * @property \App\Model\Table\CoursesTable&\Cake\ORM\Association\BelongsTo $Courses
 * @property \App\Model\Table\DaysTable&\Cake\ORM\Association\BelongsTo $Days
 *
 * @method \App\Model\Entity\Horari newEmptyEntity()
 * @method \App\Model\Entity\Horari newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Horari[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Horari get($primaryKey, $options = [])
 * @method \App\Model\Entity\Horari findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Horari patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Horari[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Horari|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Horari saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Horari[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Horari[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Horari[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Horari[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class HorarisTable extends Table
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

        $this->setTable('horaris');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Courses', [
            'foreignKey' => 'course_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Days', [
            'foreignKey' => 'day_id',
            'joinType' => 'INNER',
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
            ->integer('course_id')
            ->notEmptyString('course_id');

        $validator
            ->integer('day_id')
            ->notEmptyString('day_id');

        $validator
            ->time('horainici')
            ->requirePresence('horainici', 'create')
            ->notEmptyTime('horainici');

        $validator
            ->time('horafinal')
            ->requirePresence('horafinal', 'create')
            ->notEmptyTime('horafinal');

        $validator
            ->decimal('durada')
            ->requirePresence('durada', 'create')
            ->notEmptyString('durada');

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
        $rules->add($rules->existsIn('course_id', 'Courses'), ['errorField' => 'course_id']);
        $rules->add($rules->existsIn('day_id', 'Days'), ['errorField' => 'day_id']);

        return $rules;
    }
}
