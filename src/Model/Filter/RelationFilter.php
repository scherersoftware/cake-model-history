<?php

namespace ModelHistory\Model\Filter;

use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

class RelationFilter extends Filter
{
    /**
     * {@inheritDoc}
     */
    public function save($fieldname, $value, $model = null) {
        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function display($fieldname, $value, $model = null)
    {
        $tableString = str_replace('_id', '', $fieldname);
        $tableName = Inflector::camelize(Inflector::pluralize($tableString));
        $table = TableRegistry::get($tableName);

        $historizableBehavior = TableRegistry::get($model)->behaviors()->get('Historizable');
        if (is_object($historizableBehavior) && method_exists($historizableBehavior, 'getRelationLink')) {
            return $historizableBehavior->getRelationLink($fieldname, $value);
        }

        return $value;
    }
}
