<?php
namespace Media\Model\Behavior;

use Cake\Collection\Collection;
use Cake\Collection\Iterator\MapReduce;
use Cake\Event\Event;
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
        if (!isset($options['media']) || $options['media'] === false) {
            return;
        }

        $fields = [];
        if ($options['media'] === true) {
            $fields = array_keys($this->_fields);
        } else {
            $fields = (array) $options['media'];
        }


        $mapper = function ($row, $key, MapReduce $mapReduce) use ($fields) {


            foreach ($this->_fields as $fieldName => $field) {


                if (!in_array($fieldName, $fields)) {
                    continue;
                }

                if ($field['mode'] == self::MODE_TABLE) {
                    $row[$fieldName] = $this->_resolveFromTable($row, $fieldName, $field);

                } elseif (isset($row[$fieldName]) && !empty($row[$fieldName])) {

                    switch ($field['mode']) {
                        case self::MODE_INLINE:
                            $row[$fieldName] = $this->_resolveFromInline($row[$fieldName], $field);
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

                } else {
                }

            }

            $mapReduce->emitIntermediate($row, $key);
        };

        $reducer = function ($bucket, $name, MapReduce $mapReduce) {
            $mapReduce->emit($bucket[0], $name);
        };

        $query->mapReduce($mapper, $reducer);
    }

    public function beforeSave(Event $event, Entity $entity, $options)
    {
        foreach ($this->_fields as $fieldName => $field) {
            //if (is_object($entity->$fieldName)) {
            //}
        }
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