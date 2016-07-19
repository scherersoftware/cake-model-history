<?php
namespace ModelHistory\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
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
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $this->schema()->columnType('data', 'json');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator->add('data', 'custom', [
            'rule' => function ($value, $context) {
                if ($context['data']['action'] != ModelHistory::ACTION_COMMENT) {
                    return true;
                }
                return !empty($value['comment']);
            },
            'message' => __d('model_history', 'comment_empty')
        ]);
        return $validator;
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
            'data' => null
        ], $options);

        if (!$options['data']) {
            $options['data'] = $entity->toArray();
        }

        if ($action === ModelHistory::ACTION_DELETE) {
            $options['data'] = $entity->toArray();
        }

        if ($action === ModelHistory::ACTION_UPDATE && $options['dirtyFields']) {
            $newData = [];
            foreach ($options['dirtyFields'] as $field) {
                if (isset($options['data'][$field])) {
                    $newData[$field] = $options['data'][$field];
                }
            }
            $options['data'] = $newData;
        }

        // Obfuscate password fields
        $obfuscatedFields = TableRegistry::get($entity->source())->getObfuscatedFields();
        if (!empty($options['data']) && !empty($obfuscatedFields)) {
            foreach ($options['data'] as $fieldName => $data) {
                if (in_array($fieldName, $obfuscatedFields)) {
                    $options['data'][$fieldName] = '********';
                }
            }
        }

        $entry = $this->newEntity([
            'model' => $this->getEntityModel($entity),
            'foreign_key' => $entity->id,
            'action' => $action,
            'data' => $options['data'],
            'user_id' => $userId,
            'revision' => $this->getNextRevisionNumberForEntity($entity)
        ]);
        $this->save($entry);
        return $entry;
    }

    /**
     * Add comment
     *
     * @param EntityInterface $entity Entity to add the comment to
     * @param string $comment Comment
     * @param string $userId User which wrote the note
     * @return ModelHistory
     */
    public function addComment(EntityInterface $entity, $comment, $userId = null)
    {
        return $this->add($entity, ModelHistory::ACTION_COMMENT, $userId, [
            'data' => [
                'comment' => $comment
            ]
        ]);
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
                'model' => $this->getEntityModel($entity),
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

    /**
     * Extracts the string to be saved to the model field from an entity
     *
     * @param EntityInterface $entity Entity
     * @return string
     */
    public function getEntityModel(EntityInterface $entity)
    {
        $source = $entity->source();
        if (substr($source, -5) == 'Table') {
            $source = substr($source, 0, -5);
        }
        return $source;
    }

    /**
     * getEntityWithHistory function
     *
     * @param string $model Model
     * @param string $foreignKey ForeignKey
     * @param array $options Options
     * @return void
     */
    public function getEntityWithHistory($model, $foreignKey, array $options = [])
    {
        $Table = TableRegistry::get($model);
        $userFields = $Table->getUserNameFields();
        $options = Hash::merge([
            'contain' => [
                'ModelHistory' => [
                    'fields' => [
                        'id',
                        'user_id',
                        'action',
                        'revision',
                        'created',
                        'model',
                        'foreign_key',
                        'data'
                    ],
                    'sort' => ['ModelHistory.revision DESC'],
                    'Users' => [
                        'fields' => $userFields
                    ]
                ]
            ]
        ], $options);
        $entity = $Table->get($foreignKey, $options);

        return $entity;
    }
    
    /**
     * get Model History
     *
     * @param string $model model name
     * @param string $foreignKey foreign key
     * @return array
     */
    public function getModelHistory($model, $foreignKey)
    {
        return $this->find()
            ->where([
                'model' => $model,
                'foreign_key' => $foreignKey
            ])
            ->order(['revision' => 'DESC'])
            ->contain(['Users']);
    }
}