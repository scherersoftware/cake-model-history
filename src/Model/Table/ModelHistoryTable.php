<?php
namespace ModelHistory\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use ModelHistory\Model\Entity\ModelHistory;

/**
 * ModelHistory Model
 */
class ModelHistoryTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('model_history');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->schema()->columnType('data', 'json');
    }

    /**
     * Add a record to the ModelHistory
     *
     * @param Table $table Table Object
     * @param EntityInterface $entity Entity
     * @param string $action One of ModelHistory::ACTION_*
     * @return ModelHistory
     */
    public function add(Table $table, EntityInterface $entity, $action)
    {
        $data = $entity->toArray();
        if ($action === ModelHistory::ACTION_DELETE) {
            $data = null;
        }

        $entry = $this->newEntity([
            'model' => $entity->source(),
            'foreign_key' => $entity->id,
            'action' => $action,
            'data' => $data
        ]);
        return $this->save($entry);
    }
}
