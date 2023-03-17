<?php
declare(strict_types=1);

namespace Media\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Media\Model\Entity\MediaAttachment;

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
    public function initialize(array $config): void
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
            'translationTable' => 'Media.MediaAttachmentsI18n',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): \Cake\Validation\Validator
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
     * @param \Media\Model\Entity\MediaAttachment $entity
     * @return bool|\Cake\Datasource\EntityInterface
     */
    public function saveAttachment(MediaAttachment $entity)
    {
        return $this->save($entity);
    }
}
