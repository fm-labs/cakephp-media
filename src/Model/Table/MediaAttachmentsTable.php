<?php
namespace Media\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Attachments Model
 *
 */
class MediaAttachmentsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('attachments');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    public function enableI18n()
    {
        $this->addBehavior('Translate', [
            'fields' => ['title', 'desc_text'],
            'translationTable' => 'AttachmentsI18n',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('id', 'create');

        $validator
            ->allowEmptyString('model');

        $validator
            ->add('modelid', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('modelid');

        $validator
            ->allowEmptyString('scope');

        $validator
            ->allowEmptyString('type');

        $validator
            ->requirePresence('filepath', 'create')
            ->notEmptyString('filepath');

        $validator
            ->requirePresence('filename', 'create')
            ->notEmptyString('filename');

        $validator
            ->allowEmptyString('title');

        $validator
            ->allowEmptyString('desc_text');

        $validator
            ->allowEmptyString('mimetype');

        $validator
            ->add('filesize', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('filesize');

        return $validator;
    }

    /**
     * @param Attachment $entity
     * @return bool|EntityInterface
     */
    public function saveAttachment(Attachment $entity)
    {
        return $this->save($entity);
    }
}
