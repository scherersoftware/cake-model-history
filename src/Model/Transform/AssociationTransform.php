<?php

namespace ModelHistory\Model\Transform;

use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

class AssociationTransform extends Transform
{
    /**
     * {@inheritDoc}
     */
    public function save($fieldname, $cfg, $entity = null)
    {
        return $entity[$cfg['associationKey']];
    }

    /**
     * {@inheritDoc}
     */
    public function display($fieldname, $value, $model = null)
    {
        $tableName = Inflector::camelize(Inflector::pluralize(str_replace('_id', '', $fieldname)));
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
