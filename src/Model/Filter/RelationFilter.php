<?php

namespace ModelHistory\Model\Filter;

use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

class RelationFilter extends Filter
{
    /**
     * {@inheritDoc}
     */
    public function save($fieldname, $value) {
        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function display($fieldname, $value)
    {
        $tableString = str_replace('_id', '', $value);
        $tableName = Inflector::camelize(Inflector::pluralize($tableString));
        $table = TableRegistry::get($tableName);

        $historizableBehavior = $table->behaviors()->get('Historizable');
        if (is_object($historizableBehavior) && method_exists($historizableBehavior, 'getRelationLink')) {
            return $table->behaviors()->get('Historizable')->getRelationLink($value, $value);
        }

        return $value;
    }
}
