<?php
namespace Media\Model\Behavior;

use Cake\Collection\Collection;
use Cake\Collection\Iterator\MapReduce;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Network\Exception\NotImplementedException;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Media\Model\Entity\MediaFile;
use Media\Model\Entity\MediaFileCollection;
use Media\Model\Table\MediaAttachmentsTable;

class MediaBehavior extends \Cake\ORM\Behavior
{

    const MODE_INLINE = 'inline';
    const MODE_TABLE = 'table';
    const MODE_TEXT = 'text';
    const MODE_HTML = 'html';

    protected $_defaultConfig = [
        // Reference Model
        'model' => null,
        // List of observable fields
        'fields' => [],
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
    ];

    /**
     * @var array List of configured attachment fields
     */
    protected $_fields = [];

    /**
     * @param array $config
     */
    public function initialize(array $config)
    {
        $this->_config['model'] = ($this->_config['model']) ?: $this->_table->alias();

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
                $this->_table->schema()->columnType($field, 'media_file');
            }
        }
    }

    /**
     * @param Query $query
     * @param array $options
     * @return Query
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
     * @param Event $event
     * @param Query $query
     * @param array $options
     * @param $primary
     */
    public function beforeFind(Event $event, Query $query, $options, $primary)
    {
        //if (!isset($options['media']) && $primary) {
        //    $options['media'] = true; //@TODO Make eager-loading configurable
        //}

        if (!isset($options['media']) || isset($options['media']) && $options['media'] === false) {
            return;
        }

        $fields = [];
        if ($options['media'] === true) {
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
                            break;

                        case self::MODE_TABLE:
                            throw new \LogicException('MediaBehavior: A media file in table mode can not have a field: ' . $fieldName);
                            break;

                        default:
                            throw new \LogicException('MediaBehavior: Misconfigured field: ' . $fieldName);
                            break;
                    }
                }

                if ($row instanceof EntityInterface) {
                    $row->set($fieldName, $fieldMedia);
                    $row->dirty($fieldName, false);

                    /*
                    //@TODO Refactor media url injection, as this is qNd and has bad performance (read vproperties for every field is bad)
                    if ($fieldMedia && !$field['multiple']) {
                        $virtualUrlField = $fieldName . '_url';

                        $_virtual = $row->virtualProperties();
                        $_virtual[] = $virtualUrlField;
                        $row->virtualProperties($_virtual);

                        $row->accessible($virtualUrlField, true);
                        $row->set($virtualUrlField, $fieldMedia->getUrl(true));
                        $row->dirty($virtualUrlField, false);
                    }
                    */
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

    public function afterSave(Event $event, Entity $entity, $options)
    {
        if ($entity->isNew()) {
            return true;
        }

        foreach ($this->_fields as $fieldName => $field) {
            if ($entity->get($fieldName) && $field['mode'] === "table") {
                debug($entity->get($fieldName));

                $attachment = $this->_getAttachmentsModel($fieldName)->find()->where([
                    'model' => $this->_modelName(),
                    'modelid' => $entity->id,
                    'scope' => $fieldName
                ])->first();

                if (!$attachment) {
                    $attachment = $this->_getAttachmentsModel($fieldName)->newEntity();
                    $attachment->model = $this->_modelName();
                    $attachment->modelid = $entity->id;
                    $attachment->scope = $fieldName;
                }

                $attachment->filepath = $entity->get($fieldName);

                if (!$this->_getAttachmentsModel($fieldName)->save($attachment)) {
                    Log::error('MediaBehavior:afterSave: Failed to save attachment: ' . json_encode($attachment->errors()));
                }
            }
        }
    }

    /**
     * Return model alias including plugin prefix with dot notation.
     * Compatible with TableRegistry::get()
     *
     * Example: Plugin Blog has a model table named PostsTable
     *   The function would return 'Blog.Posts'
     *
     * @return null|string Model alias with plugin prefix (e.g. 'Blog.Posts')
     */
    protected function _modelName()
    {
        $plugin = null;
        $tableName = $this->_table->alias();
        list($namespace, ) = namespaceSplit(get_class($this->_table));
        if ($namespace && (($pos = strpos($namespace, '\\')) !== false)) {
            $plugin = substr($namespace, 0, $pos);
            if ($plugin == 'App' || $plugin == 'Cake') {
                return $tableName;
            }
        }

        return join('.', [$plugin, $tableName]);
    }

    /**
     * @param string $filePath Relative file path to configured dataDir
     * @param array $field Field config
     * @return array|MediaFile|Collection
     */
    protected function _resolveFromInline($filePath, $field)
    {
        //debug("resolve media file " . $this->_table->alias() . ":" . $filePath);
        $config =& $this->_config;

        $resolver = function ($filePath) use ($field, $config) {

            if (!$filePath) {
                return;
            }

            //debug("resolving " . $filePath);
            $file = new $field['entityClass']();
            $file->config = $field['config'];
            $file->path = $filePath;

            return $file;
        };

        if ($field['multiple'] && is_array($filePath)) {
            $files = [];
            foreach ($filePath as $_filePath) {
                $files[] = $resolver($_filePath);
            }
            // The marshaller does not accept arrays,
            // so we use a Collection -> solved by using a custom data type 'media_file'
            return $files;
            //return new MediaFileCollection($files);
        } else {
            return $resolver($filePath);
        }
    }

    protected function _resolveFromTable($row, $fieldName, $field)
    {

        //debug("Resolving db attachments for field " . $fieldName);
        $config =& $this->_config;
        $Attachments = $this->_getAttachmentsModel($fieldName);
        $params = [
            'MediaAttachments.model' => $config['model'],
            'MediaAttachments.modelid' => $row->id,
            'MediaAttachments.scope' => $fieldName,
        ];
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
     * @param $field
     * @return MediaAttachmentsTable
     */
    protected function _getAttachmentsModel($field)
    {
        $Model = TableRegistry::get($this->_fields[$field]['attachmentsTable']);
        if ($this->_fields[$field]['i18n'] && $this->_table && $this->_table->hasBehavior('Translate')) {
            $parentLocale = $this->_table->locale();
            $Model->enableI18n();
            $Model->locale($parentLocale);
        }

        return $Model;
    }
}
