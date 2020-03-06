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
            'translationTable' => 'AttachmentsI18n'
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
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('model');

        $validator
            ->add('modelid', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('modelid');

        $validator
            ->allowEmpty('scope');

        $validator
            ->allowEmpty('type');

        $validator
            ->requirePresence('filepath', 'create')
            ->notEmpty('filepath');

        $validator
            ->requirePresence('filename', 'create')
            ->notEmpty('filename');

        $validator
            ->allowEmpty('title');

        $validator
            ->allowEmpty('desc_text');

        $validator
            ->allowEmpty('mimetype');

        $validator
            ->add('filesize', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('filesize');

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
