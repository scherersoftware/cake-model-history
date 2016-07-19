<?php
namespace ModelHistory\Model\Entity;

use Cake\ORM\Entity;

/**
 * ModelHistory Entity.
 */
class ModelHistory extends Entity
{

    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_COMMENT = 'comment';

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
        return $data;
    }
}
