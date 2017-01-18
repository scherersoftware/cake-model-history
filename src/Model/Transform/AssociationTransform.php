<?php

namespace ModelHistory\Model\Transform;

use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

class AssociationTransform extends Transform
{
    /**
     * {@inheritDoc}
     */
    public function save($fieldname, $config, $entity = null)
    {
        return $entity[$config['associationKey']];
    }

    /**
     * {@inheritDoc}
     */
    public function display($fieldname, $value, $model = null)
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
