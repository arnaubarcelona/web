<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SortidatornsCourses Model
 *
 * @property \App\Model\Table\SortidatornsTable&\Cake\ORM\Association\BelongsTo $Sortidatorns
 * @property \App\Model\Table\CoursesTable&\Cake\ORM\Association\BelongsTo $Courses
 *
 * @method \App\Model\Entity\SortidatornsCourse newEmptyEntity()
 * @method \App\Model\Entity\SortidatornsCourse newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\SortidatornsCourse[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SortidatornsCourse get($primaryKey, $options = [])
 * @method \App\Model\Entity\SortidatornsCourse findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\SortidatornsCourse patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SortidatornsCourse[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SortidatornsCourse|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SortidatornsCourse saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SortidatornsCourse[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SortidatornsCourse[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\SortidatornsCourse[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SortidatornsCourse[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SortidatornsCoursesTable extends Table
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

        $this->setTable('sortidatorns_courses');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Sortidatorns', [
            'foreignKey' => 'sortidatorn_id',
        ]);
        $this->belongsTo('Courses', [
            'foreignKey' => 'course_id',
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
            ->integer('sortidatorn_id')
            ->allowEmptyString('sortidatorn_id');

        $validator
            ->integer('course_id')
            ->allowEmptyString('course_id');

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
        $rules->add($rules->existsIn('sortidatorn_id', 'Sortidatorns'), ['errorField' => 'sortidatorn_id']);
        $rules->add($rules->existsIn('course_id', 'Courses'), ['errorField' => 'course_id']);

        return $rules;
    }
}
