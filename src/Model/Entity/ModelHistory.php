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
        // Obfuscate password fields
        $obfuscatedFields = TableRegistry::get($this->model)->getObfuscatedFields();
        if (!empty($data) && !empty($obfuscatedFields)) {
            foreach ($data as $fieldName => $value) {
                if (in_array($fieldName, $obfuscatedFields)) {
                    $data[$fieldName] = '********';
                }
            }
        }
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
        if ($value === true) {
            return 'true';
        } elseif ($value === false) {
            return 'false';
        } elseif ($value === null) {
            return 'NULL';
        } elseif ($value === '') {
            return '""';
        } else {
            return $value;
        }
    }
}
