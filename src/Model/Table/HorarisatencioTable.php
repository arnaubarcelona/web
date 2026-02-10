<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Horarisatencio Model
 *
 * @property \App\Model\Table\DaysTable&\Cake\ORM\Association\BelongsTo $Days
 *
 * @method \App\Model\Entity\Horarisatencio newEmptyEntity()
 * @method \App\Model\Entity\Horarisatencio newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Horarisatencio[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Horarisatencio get($primaryKey, $options = [])
 * @method \App\Model\Entity\Horarisatencio findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Horarisatencio patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Horarisatencio[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Horarisatencio|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Horarisatencio saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Horarisatencio[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Horarisatencio[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Horarisatencio[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Horarisatencio[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class HorarisatencioTable extends Table
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

        $this->setTable('horarisatencio');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Days', [
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
            ->integer('day_id')
            ->allowEmptyString('day_id');

        $validator
            ->date('specialdate')
            ->allowEmptyDate('specialdate');

        $validator
            ->time('horainici')
            ->requirePresence('horainici', 'create')
            ->notEmptyTime('horainici');

        $validator
            ->time('horafinal')
            ->requirePresence('horafinal', 'create')
            ->notEmptyTime('horafinal');

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
        $rules->add($rules->existsIn('day_id', 'Days'), ['errorField' => 'day_id']);

        return $rules;
    }
}
