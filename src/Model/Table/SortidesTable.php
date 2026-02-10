<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Sortides Model
 *
 * @property \App\Model\Table\SortidatornsTable&\Cake\ORM\Association\HasMany $Sortidatorns
 *
 * @method \App\Model\Entity\Sortide newEmptyEntity()
 * @method \App\Model\Entity\Sortide newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Sortide[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Sortide get($primaryKey, $options = [])
 * @method \App\Model\Entity\Sortide findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Sortide patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Sortide[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Sortide|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Sortide saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Sortide[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Sortide[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Sortide[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Sortide[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SortidesTable extends Table
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

        $this->setTable('sortides');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->hasMany('Sortidatorns', [
            'foreignKey' => 'sortide_id',
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
            ->integer('tipuspagamentsortida_id')
            ->allowEmptyString('tipuspagamentsortida_id');

        $validator
            ->scalar('titol')
            ->maxLength('titol', 255)
            ->allowEmptyString('titol');

        $validator
            ->scalar('lloc')
            ->maxLength('lloc', 255)
            ->allowEmptyString('lloc');

        $validator
            ->scalar('adreca')
            ->maxLength('adreca', 255)
            ->allowEmptyString('adreca');

        $validator
            ->scalar('nompunttrobada')
            ->maxLength('nompunttrobada', 255)
            ->allowEmptyString('nompunttrobada');

        $validator
            ->scalar('urlmapslloc')
            ->allowEmptyString('urlmapslloc');

        $validator
            ->scalar('urlmapstrobada')
            ->allowEmptyString('urlmapstrobada');

        $validator
            ->scalar('observacions')
            ->allowEmptyString('observacions');

        $validator
            ->scalar('url')
            ->maxLength('url', 500)
            ->allowEmptyString('url');

        return $validator;
    }
}
