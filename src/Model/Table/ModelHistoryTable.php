<?php
namespace ModelHistory\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;
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
     * @param EntityInterface $entity Entity
     * @param string $action One of ModelHistory::ACTION_*
     * @param string $userId User ID to assign this history entry to
     * @param array $options Additional options
     * @return ModelHistory
     */
    public function add(EntityInterface $entity, $action, $userId = null, array $options = [])
    {
        $options = Hash::merge([
            'dirtyFields' => null,
        ], $options);
        
        $data = $entity->toArray();
        if ($action === ModelHistory::ACTION_DELETE) {
            $data = null;
        }

        if ($action === ModelHistory::ACTION_UPDATE && $options['dirtyFields']) {
            $newData = [];
            foreach ($options['dirtyFields'] as $field) {
                $newData[$field] = $data[$field];
            }
            $data = $newData;
        }

        $entry = $this->newEntity([
            'model' => $entity->source(),
            'foreign_key' => $entity->id,
            'action' => $action,
            'data' => $data,
            'user_id' => $userId,
            'revision' => $this->getNextRevisionNumberForEntity($entity)
        ]);
        return $this->save($entry);
    }

    /**
     * Handles the revision sequence
     *
     * @param EntityInterface $entity Entity to get the revision number for
     * @return int
     */
    public function getNextRevisionNumberForEntity(EntityInterface $entity)
    {
        $revision = 1;
        $last = $this->find()
            ->select('revision')
            ->where([
                'model' => $entity->source(),
                'foreign_key' => $entity->id
            ])
            ->order(['revision DESC'])
            ->hydrate(false)
            ->first();

        if (isset($last['revision'])) {
            $revision = $last['revision'] + 1;
        }
        return $revision;
    }
}
