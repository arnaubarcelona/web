<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Pagines Model
 *
 * @method \App\Model\Entity\Pagine newEmptyEntity()
 * @method \App\Model\Entity\Pagine newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Pagine[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Pagine get($primaryKey, $options = [])
 * @method \App\Model\Entity\Pagine findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Pagine patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Pagine[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Pagine|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Pagine saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Pagine[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Pagine[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Pagine[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Pagine[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PaginesTable extends Table
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

        $this->setTable('pagines');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
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
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('body')
            ->maxLength('body', 16777215)
            ->requirePresence('body', 'create')
            ->notEmptyString('body');

        $validator
            ->scalar('order_code')
            ->maxLength('order_code', 32)
            ->requirePresence('order_code', 'create')
            ->notEmptyString('order_code');

        $validator
            ->boolean('visible')
            ->notEmptyString('visible');

        $validator
            ->boolean('main')
            ->notEmptyString('main');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        return $validator;
    }
}
