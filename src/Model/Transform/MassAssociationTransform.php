<?php

namespace ModelHistory\Model\Transform;

use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

class MassAssociationTransform extends Transform
{
    /**
     * {@inheritDoc}
     */
    public function save($fieldname, $config, $entity = null)
    {
        if (!empty($entity[$config['name']])) {
            $assocData = $entity[$config['name']];
            if (is_array($assocData)) {
                $data = [];
                foreach ($assocData as $assocEntity) {
                    $entityTable = TableRegistry::get($assocEntity->source());
                    $data[] = $assocEntity->{$entityTable->displayField()};
                }

                return $data;
            }
        }

        return $entity[$config['name']];
    }

    /**
     * {@inheritDoc}
     */
    public function display($fieldname, $value, $model = null)
    {
        if (is_array($value)) {
            return join(', ', $value);
        }

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
