<?php
declare(strict_types=1);

namespace Media\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MediaAttachmentsI18n Model
 *
 * @method \Media\Model\Entity\MediaAttachmentsI18n newEmptyEntity()
 * @method \Media\Model\Entity\MediaAttachmentsI18n newEntity(array $data, array $options = [])
 * @method \Media\Model\Entity\MediaAttachmentsI18n[] newEntities(array $data, array $options = [])
 * @method \Media\Model\Entity\MediaAttachmentsI18n get($primaryKey, $options = [])
 * @method \Media\Model\Entity\MediaAttachmentsI18n findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \Media\Model\Entity\MediaAttachmentsI18n patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Media\Model\Entity\MediaAttachmentsI18n[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \Media\Model\Entity\MediaAttachmentsI18n|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Media\Model\Entity\MediaAttachmentsI18n saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Media\Model\Entity\MediaAttachmentsI18n[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \Media\Model\Entity\MediaAttachmentsI18n[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \Media\Model\Entity\MediaAttachmentsI18n[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \Media\Model\Entity\MediaAttachmentsI18n[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class MediaAttachmentsI18nTable extends Table
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

        $this->setTable('attachments_i18n');
        $this->setDisplayField('id');
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
            ->scalar('locale')
            ->maxLength('locale', 6)
            ->requirePresence('locale', 'create')
            ->notEmptyString('locale');

        $validator
            ->scalar('model')
            ->maxLength('model', 255)
            ->requirePresence('model', 'create')
            ->notEmptyString('model');

        $validator
            ->integer('foreign_key')
            ->requirePresence('foreign_key', 'create')
            ->notEmptyString('foreign_key');

        $validator
            ->scalar('field')
            ->maxLength('field', 255)
            ->requirePresence('field', 'create')
            ->notEmptyString('field');

        $validator
            ->scalar('content')
            ->allowEmptyString('content');

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
        $rules->add($rules->isUnique(['locale', 'model', 'foreign_key', 'field']), ['errorField' => 'locale']);

        return $rules;
    }
}
