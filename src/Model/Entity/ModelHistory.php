<?php
namespace ModelHistory\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * ModelHistory Entity.
 */
class ModelHistory extends Entity
{
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_COMMENT = 'comment';

    const CONTEXT_TYPE_CONTROLLER = 'controller';
    const CONTEXT_TYPE_SHELL = 'shell';
    const CONTEXT_TYPE_SLUG = 'slug';

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'model' => true,
        'foreign_key' => true,
        'user_id' => true,
        'action' => true,
        'data' => true,
        'context' => true,
        'context_slug' => true,
        'context_type' => true,
        'user' => true,
        'revision' => true,
    ];

    /**
     * getter for data
     *
     * @param string $data data
     * @return array
     */
    protected function _getData($data)
    {
        // Stringify empty values
        if (!empty($data)) {
            foreach ($data as $fieldName => $value) {
                $data[$fieldName] = $this->_stringifyEmptyValue($value);
            }
        }

        return $data;
    }

    /**
     * Transform null and boolean values to their string representation.
     *
     * @param  mixed  $value  The Value to be checked
     * @return mixed
     */
    protected function _stringifyEmptyValue($value)
    {
        if ($value === null) {
            return 'NULL';
        } elseif ($value === '') {
            return '""';
        } else {
            return $value;
        }
    }

    /**
     * Retrieve available context types
     *
     * @return array
     */
    public static function getContextTypes()
    {
        return [
            self::CONTEXT_TYPE_CONTROLLER => __d('model_history', 'context.type.controller'),
            self::CONTEXT_TYPE_SHELL => __d('model_history', 'context.type.shell'),
            self::CONTEXT_TYPE_SLUG => __d('model_history', 'context.type.slug')
        ];
    }
}
