<?php
declare(strict_types = 1);

namespace ModelHistory\Model\Transform;

use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

class AssociationTransform extends Transform
{
    /**
     * Amend the data before saving.
     *
     * @param string $fieldname Field name
     * @param array $config field config
     * @param \Cake\Datasource\EntityInterface $entity entity
     * @return mixed
     */
    public function save(string $fieldname, array $config, EntityInterface $entity = null)
    {
        return $entity[$config['associationKey']];
    }

    /**
     * Amend the data before displaying.
     *
     * @param string $fieldname Field name
     * @param mixed  $value Value to be amended
     * @param string $model Optional model to be used
     * @return mixed
     */
    public function display(string $fieldname, $value, string $model = null)
    {
        $tableName = Inflector::camelize(Inflector::pluralize(str_replace('_id', '', $fieldname)));
        $table = TableRegistry::get($tableName);

        $tableConfig = [];

        $historizableBehavior = TableRegistry::get($model, $tableConfig)->behaviors()->get('Historizable');
        if (is_object($historizableBehavior) && method_exists($historizableBehavior, 'getRelationLink')) {
            return $historizableBehavior->getRelationLink($fieldname, $value);
        }

        return $value;
    }
}
