<?php
declare(strict_types=1);

namespace Media\Model\Behavior;

use Cake\Collection\Iterator\MapReduce;
use Cake\Datasource\EntityInterface;
use Cake\Filesystem\Folder;
use Cake\Http\Exception\NotImplementedException;
use Cake\Log\Log;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Media\MediaManager;
use Media\Model\Entity\MediaFile;
use Upload\Exception\UploadException;
use Upload\Uploader;

class MediaBehavior extends \Cake\ORM\Behavior
{
    public const MODE_INLINE = 'inline';
    public const MODE_TABLE = 'table';
    public const MODE_TEXT = 'text';
    public const MODE_HTML = 'html';

    protected $_defaultConfig = [
        // Reference Model
        'model' => null,
        // List of observable fields
        'fields' => [],

        'implementedMethods' => [
            'getMediaFields' => 'getFields',
        ],
    ];

    protected $_defaultFieldConfig = [
        // Storage Mode
        'mode' => 'inline',
        // Media config name
        'config' => 'default',
        // Entity class location
        'entityClass' => '\\Media\\Model\\Entity\\MediaFile',
        // Multiple
        'multiple' => false,
        //### MODE TABLE CONFIG OPTIONS ###
        // Attachments table class. Defaults to 'Media.MediaAttachments'
        'attachmentsTable' => 'Media.MediaAttachments',
        // Use i18n mode
        'i18n' => false,
        // upload
        'upload' => false,
    ];

    /**
     * @var array List of configured attachment fields
     */
    protected $_fields = [];

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config): void
    {
        $this->_config['model'] = $this->_config['model'] ?: $this->_table->getAlias();

        foreach ($this->_config['fields'] as $field => $_config) {
            if (is_numeric($field)) {
                $field = $_config;
                $_config = [];
            }
            $_config = array_merge($this->_defaultFieldConfig, $_config);
            $this->_fields[$field] = $_config;
            $this->_fields[$field]['model'] = $this->_config['model'];

            // apply special field type to schema
            if ($_config['multiple'] === true && $_config['mode'] !== self::MODE_TABLE) {
                $this->_table->getSchema()->setColumnType($field, 'media_file');
            }
        }
    }

    /**
     * @return array List of configured fields
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * @param \Cake\ORM\Query $query Query object
     * @param array $options Finder options
     * @return \Cake\ORM\Query
     */
    public function findMedia(Query $query, array $options)
    {
        $options = array_merge(['media' => true], $options);

        return $query->applyOptions($options);
    }

    /**
     * 'beforeFind' callback
     *
     * Applies a MapReduce to the query, which resolves attachment info
     * if an attachment field is present in the query results.
     *
     * @param \Cake\Event\Event $event The event object
     * @param \Cake\ORM\Query $query The query object
     * @param array $options Finder options
     * @param bool $primary Primary flag
     * @return void
     */
    public function beforeFind(\Cake\Event\EventInterface $event, Query $query, $options, $primary)
    {
        //if (!isset($options['media']) && $primary) {
        //    $options['media'] = true; //@TODO Make eager-loading configurable
        //}

        if (/*!isset($options['media']) || */isset($options['media']) && $options['media'] === false) {
            //debug("Media disabled in this find operatoin " . get_class($this->_table));
            return;
        }

        $fields = [];
        if (!isset($options['media']) || $options['media'] === true) {
            $fields = array_keys($this->_fields);
        } else {
            $fields = (array)$options['media'];
        }

        $mapper = function ($row, $key, MapReduce $mapReduce) use ($fields) {
            foreach ($this->_fields as $fieldName => $field) {
                if (!in_array($fieldName, $fields)) {
                    continue;
                }

                $fieldMedia = null;

                if ($field['mode'] == self::MODE_TABLE) {
                    $fieldMedia = $this->_resolveFromTable($row, $fieldName, $field);
                } elseif (isset($row[$fieldName]) && !empty($row[$fieldName])) {
                    switch ($field['mode']) {
                        case self::MODE_INLINE:
                            $fieldMedia = $this->_resolveFromInline($row[$fieldName], $field);
                            break;

                        case self::MODE_TEXT:
                        case self::MODE_HTML:
                            throw new NotImplementedException('MediaBehavior: ' . $field['mode'] . ' not implemented yet for field: ' . $fieldName);

                        case self::MODE_TABLE:
                            throw new \LogicException('MediaBehavior: A media file in table mode can not have a field: ' . $fieldName);

                        default:
                            throw new \LogicException('MediaBehavior: Misconfigured field: ' . $fieldName);
                    }
                }
                if ($row instanceof EntityInterface) {
                    $row->set($fieldName, $fieldMedia);
                    $row->setDirty($fieldName, false);
                } else {
                    $row[$fieldName] = $fieldMedia;
                }
            }

            $mapReduce->emitIntermediate($row, $key);
        };

        $reducer = function ($bucket, $name, MapReduce $mapReduce) {
            $mapReduce->emit($bucket[0], $name);
        };

        $query->mapReduce($mapper, $reducer);
    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @param \Cake\ORM\Entity $entity The entity object
     * @param \ArrayObject $options Finder options
     * @return void|bool
     */
    public function beforeSave(\Cake\Event\EventInterface $event, Entity $entity, \ArrayObject $options)
    {
        foreach ($this->_fields as $field => $fieldConfig) {
            $uploadField = $field . '_upload';
            $uploadOptions = ['exceptions' => true];
            $value = null;

            $upload = $entity->get($uploadField);
            if ($fieldConfig['upload'] && is_array($upload)) {
                if (isset($upload['error']) && $upload['error'] == 4) { // err 4 == no file uploaded
                    continue;
                }

                debug("Uploading ...");

                try {
                    // set upload dir. create it if it does not exist
                    $uploadDir = Inflector::tableize($this->_table->getAlias());

                    $mm = MediaManager::get($fieldConfig['config']);
                    $uploadBasePath = $mm->getBasePath();

                    $uploadPath = $uploadBasePath . $uploadDir . DS;
                    if (!is_dir($uploadPath)) {
                        debug("Upload path $uploadPath does not exist. Attempting to create it.");
                        $Folder = new Folder($uploadPath, true);
                        if (!$Folder->cd($uploadPath)) {
                            debug("Failed to create upload dir");
                        }
                    }

                    $Uploader = new Uploader($fieldConfig['upload']);
                    $Uploader->setUploadDir($uploadPath);

                    if ($fieldConfig['multiple']) {
                        //@TODO Multi upload is broken
                        $Uploader->setConfig('multiple', true);
                        $value = [];
                        if ($Uploader->upload($entity->{$uploadField}, $uploadOptions)) {
                            foreach ($Uploader->getResult() as $upload) {
                                $value[] = $upload['basename'];
                            }
                        }
                        $value = join(',', $value);
                    } else {
                        $upload = $Uploader->upload($entity->{$uploadField}, $uploadOptions);
                        debug($upload);

                        $file = [
                            'config' => $fieldConfig['config'],
                            'path' => $uploadDir . '/' . $upload['basename'],
                            'size' => $upload['size'],
                            'mime_type' => $upload['type'],
                        ];

                        $mfile = new MediaFile();
                        $mfile->set($file);

                        $value = json_encode($mfile->toArray());
                    }

                    ///debug($value);

                    // flag deprecated item for removal
                    //if ($entity->$field) {
                    //    $this->_flaggedForRemoval[] = $entity->$field->source;
                    //}

                    // replace with uploaded item
                    $entity->$field = $value;

                    // clear upload field
                    unset($entity->$uploadField);
                } catch (UploadException $ex) {
                    Log::alert('AttachmentBehavior: UploadException: ' . $ex->getMessage());
                    $entity->setError($uploadField, [$ex->getMessage()]);
                    $entity->setError($field, [$ex->getMessage()]);

                    return false;
                } catch (\Exception $ex) {
                    Log::alert('AttachmentBehavior: Exception: ' . $ex->getMessage());
                    $entity->setError($uploadField, [$ex->getMessage()]);
                    $entity->setError($field, [$ex->getMessage()]);

                    return false;
                }
            } elseif ($entity->has($field)) {
                unset($entity->$uploadField);
            }
        }

        //$this->_removeFlagged();
    }

//    protected function _removeFlagged()
//    {
//        for ($i = 0; $i < count($this->_flaggedForRemoval); $i++) {
//            $path = $this->_flaggedForRemoval[$i];
//            @unlink($path);
//            //debug("unlinked $path");
//            unset($this->_flaggedForRemoval[$i]);
//        }
//    }

    /**
     * @param \Cake\Event\Event $event The event object
     * @param \Cake\ORM\Entity $entity The entity object
     * @param \ArrayObject $options Finder options
     * @return void|bool
     */
    public function afterSave(\Cake\Event\EventInterface $event, Entity $entity, \ArrayObject $options)
    {
        //debug("afterSave");
        if ($entity->isNew()) {
            return true;
        }

        foreach ($this->_fields as $fieldName => $field) {
            if ($entity->get($fieldName) && $field['mode'] === "table") {
                //debug($entity->get($fieldName));

                $attachment = $this->_getAttachmentsModel($fieldName)->find()->where([
                    'model' => $this->_modelName(),
                    'modelid' => $entity->id,
                    'scope' => $fieldName,
                ])->first();

                if (!$attachment) {
                    $attachment = $this->_getAttachmentsModel($fieldName)->newEmptyEntity();
                    $attachment->model = $this->_modelName();
                    $attachment->modelid = $entity->id;
                    $attachment->scope = $fieldName;
                }

                $attachment->filepath = $entity->get($fieldName);

                if (!$this->_getAttachmentsModel($fieldName)->save($attachment)) {
                    Log::error('MediaBehavior:afterSave: Failed to save attachment: ' . json_encode($attachment->getErrors()));
                }
            }
        }
    }

    /**
     * Return model alias including plugin prefix with dot notation.
     * Compatible with TableRegistry::getTableLocator()->get()
     *
     * Example: Plugin Blog has a model table named PostsTable
     *   The function would return 'Blog.Posts'
     *
     * @return null|string Model alias with plugin prefix (e.g. 'Blog.Posts')
     */
    protected function _modelName()
    {
        $plugin = null;
        $tableName = $this->_table->getAlias();
        [$namespace, ] = namespaceSplit(get_class($this->_table));
        if ($namespace && (($pos = strpos($namespace, '\\')) !== false)) {
            $plugin = substr($namespace, 0, $pos);
            if ($plugin == 'App' || $plugin == 'Cake') {
                return $tableName;
            }
        }

        return join('.', [$plugin, $tableName]);
    }

    /**
     * @param string $value Relative file path to configured dataDir
     * @param array $field Field config
     * @return null|array|\Media\Model\Entity\MediaFile|\Cake\Collection\Collection
     */
    protected function _resolveFromInline($value, $field)
    {
        //debug("resolve media file inline on table:" . $this->_table->getAlias() . " path:" . $value);
        if (!$value) {
            return null;
        }

        $config =& $this->_config;

        $resolver = function ($value) use ($field, $config) {
            // @TODO Use dedicated InlineMediaFile object and/or check if MediaFileInterface is attached
            /** @var \Cake\Datasource\EntityInterface $file */
            $file = new $field['entityClass']();
            //$file->config = $field['config'];

            // check if json or simple string
            if (preg_match('/^\{(.*)\}$/', $value)) {
                $_data = json_decode($value, true);
                //debug($_data);
                $file->setAccess('*', true);
                $file->set($_data);
            } else {
                $file->set('config', $field['config']);
                $file->set('path', $value);
            }

            return $file;
        };

        // @todo Multiple files do not work with json encoded media data
        if ($field['multiple']) {
            $names = explode(',', $value);
            $files = [];
            foreach ($names as $_value) {
                $files[] = $resolver($_value);
            }
            // The marshaller does not accept arrays,
            // so we use a Collection -> solved by using a custom data type 'media_file'
            return $files;
            //return new MediaFileCollection($files);
        } else {
            $file = $resolver($value);
            //debug($file->toArray());
            return $file;
        }
    }

    /**
     * Resolve from attachments table
     *
     * @param mixed $row Data row
     * @param string $fieldName Field name
     * @param array $field Field config
     * @return null|array|\Media\Model\Entity\MediaFile|\Cake\Collection\Collection
     */
    protected function _resolveFromTable($row, $fieldName, $field)
    {
        //debug("Resolving db attachments for field " . $fieldName);
        $config =& $this->_config;
        $Attachments = $this->_getAttachmentsModel($fieldName);
        $params = [
            'MediaAttachments.model' => $config['model'],
            'MediaAttachments.scope' => $fieldName,
        ];

        $rowId = $row['id'] ?? null;
        $modelIdParam = $rowId === null ? 'MediaAttachments.modelid IS' : 'MediaAttachments.modelid';
        $params[$modelIdParam] = $rowId;
        $query = $Attachments->find()->where($params);

        //@TODO Keep it dry (possible duplicate in '_resolveInlineAttachment' method)
        //@TODO Extract file extension
        // Resolve DB Attachment
        $resolver = function ($attachment) use ($field, $config) {
            if ($attachment === null) {
                return;
            }

            $filePath = $attachment->filepath;
            //$sourcePath = $config['dataDir'] . $filePath;


            $file = new $field['entityClass']();
            $file->config = $field['config'];
            $file->path = $filePath;
            $file->desc = $attachment->desc_text;

            /*
            $file = new $field['entityClass']();
            $file->name = $filePath;
            $file->source = $sourcePath;
            $file->size = $attachment->filesize;
            $file->basename = $attachment->filename;
            $file->ext = null;
            $file->desc = $attachment->desc_text;
            if ($config['dataUrl']) {
                $file->url = rtrim($config['dataUrl'], '/') . '/' . $filePath;
            }
            */

            return $file;
        };

        if ($field['multiple']) {
            $attachments = $query->all();
            $files = [];
            foreach ($attachments as $attachment) {
                $files[] = $resolver($attachment);
            }

            return $files;
        } else {
            $attachment = $query->first();

            return $resolver($attachment);
        }
    }

    /**
     * @param string $field Field name
     * @return \Media\Model\Table\MediaAttachmentsTable
     */
    protected function _getAttachmentsModel($field)
    {
        $Model = TableRegistry::getTableLocator()->get($this->_fields[$field]['attachmentsTable']);
        if ($this->_fields[$field]['i18n'] && $this->_table && $this->_table->hasBehavior('Translate')) {
            $parentLocale = $this->_table->locale();
            $Model->enableI18n();
            $Model->setLocale($parentLocale);
        }

        return $Model;
    }
}
