<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Courses Model
 *
 * @property \App\Model\Table\SubjectsTable&\Cake\ORM\Association\BelongsTo $Subjects
 * @property \App\Model\Table\ModesTable&\Cake\ORM\Association\BelongsTo $Modes
 * @property \App\Model\Table\YearsTable&\Cake\ORM\Association\BelongsTo $Years
 * @property \App\Model\Table\TornsTable&\Cake\ORM\Association\BelongsTo $Torns
 * @property \App\Model\Table\AulasTable&\Cake\ORM\Association\BelongsTo $Aulas
 * @property \App\Model\Table\HorarisTable&\Cake\ORM\Association\HasMany $Horaris
 * @property \App\Model\Table\SortidatornsTable&\Cake\ORM\Association\BelongsToMany $Sortidatorns
 *
 * @method \App\Model\Entity\Course newEmptyEntity()
 * @method \App\Model\Entity\Course newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Course[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Course get($primaryKey, $options = [])
 * @method \App\Model\Entity\Course findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Course patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Course[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Course|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Course saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Course[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Course[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Course[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Course[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CoursesTable extends Table
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

        $this->setTable('courses');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Subjects', [
            'foreignKey' => 'subject_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Modes', [
            'foreignKey' => 'mode_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Years', [
            'foreignKey' => 'year_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Torns', [
            'foreignKey' => 'torn_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Aulas', [
            'foreignKey' => 'aula_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Horaris', [
            'foreignKey' => 'course_id',
        ]);
        $this->belongsToMany('Sortidatorns', [
            'foreignKey' => 'course_id',
            'targetForeignKey' => 'sortidatorn_id',
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
            ->scalar('code')
            ->maxLength('code', 10)
            ->requirePresence('code', 'create')
            ->notEmptyString('code');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->requirePresence('propi', 'create')
            ->notEmptyString('propi');

        $validator
            ->integer('parentcourse_id')
            ->allowEmptyString('parentcourse_id');

        $validator
            ->boolean('microgrup')
            ->notEmptyString('microgrup');

        $validator
            ->integer('subject_id')
            ->notEmptyString('subject_id');

        $validator
            ->integer('level')
            ->requirePresence('level', 'create')
            ->notEmptyString('level');

        $validator
            ->integer('mode_id')
            ->notEmptyString('mode_id');

        $validator
            ->integer('trimestre')
            ->allowEmptyString('trimestre');

        $validator
            ->integer('quadrimestre')
            ->allowEmptyString('quadrimestre');

        $validator
            ->integer('year_id')
            ->notEmptyString('year_id');

        $validator
            ->integer('size')
            ->requirePresence('size', 'create')
            ->notEmptyString('size');

        $validator
            ->integer('torn_id')
            ->notEmptyString('torn_id');

        $validator
            ->integer('aula_id')
            ->notEmptyString('aula_id');

        $validator
            ->date('datainici')
            ->requirePresence('datainici', 'create')
            ->notEmptyDate('datainici');

        $validator
            ->date('datafi')
            ->requirePresence('datafi', 'create')
            ->notEmptyDate('datafi');

        $validator
            ->integer('horesanuals')
            ->allowEmptyString('horesanuals');

        $validator
            ->integer('competenciatic_id')
            ->allowEmptyString('competenciatic_id');

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
        $rules->add($rules->existsIn('subject_id', 'Subjects'), ['errorField' => 'subject_id']);
        $rules->add($rules->existsIn('mode_id', 'Modes'), ['errorField' => 'mode_id']);
        $rules->add($rules->existsIn('year_id', 'Years'), ['errorField' => 'year_id']);
        $rules->add($rules->existsIn('torn_id', 'Torns'), ['errorField' => 'torn_id']);
        $rules->add($rules->existsIn('aula_id', 'Aulas'), ['errorField' => 'aula_id']);

        return $rules;
    }
}
