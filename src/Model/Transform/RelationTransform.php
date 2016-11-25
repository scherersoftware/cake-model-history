<?php

namespace ModelHistory\Model\Transform;

use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

class RelationTransform extends Transform
{
    /**
     * {@inheritDoc}
     */
    public function save($fieldname, $value, $model = null)
    {
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

        $tableConfig = [];
        if (defined('PHPUNIT_TESTSUITE')) {
            $tableConfig = ['className' => 'ModelHistoryTestApp\Model\Table\ArticlesTable'];
        }

        $historizableBehavior = TableRegistry::get($model, $tableConfig)->behaviors()->get('Historizable');
        if (is_object($historizableBehavior) && method_exists($historizableBehavior, 'getRelationLink')) {
            return $historizableBehavior->getRelationLink($fieldname, $value);
        }

        return $value;
    }
}
