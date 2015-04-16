<?php
namespace ModelHistory\Model\Behavior;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use ModelHistory\Model\Entity\ModelHistory;

/**
 * Historizable behavior
 */
class HistorizableBehavior extends Behavior
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'userIdCallback' => null,
        'customActions' => [],
        'userNameFields' => [
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'id' => 'Users.id'
        ]
    ];

    /**
     * Instance of the ModelHistoryTable model
     *
     * @var ModelHistoryTable
     */
    public $ModelHistory;

    /**
     * Store dirty fields for entities in beforeSave to make it possible to detect
     * them in afterSave()
     *
     * @var array
     */
    protected $_dirtyFields = [];

    /**
     * Constructor hook method.
     *
     * @param array $config The configuration settings provided to this behavior.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->ModelHistory = TableRegistry::get('ModelHistory.ModelHistory');
        // Dynamically attach the hasMany relationship
        $this->_table->hasMany('ModelHistory.ModelHistory', [
            'conditions' => [
                'ModelHistory.model' => $this->_table->alias()
            ],
            'order' => ['ModelHistory.revision DESC'],
            'foreignKey' => 'foreign_key',
            'dependent' => false
        ]);
        parent::initialize($config);
    }

    /**
     * beforeSave callback
     *
     * @param Event $event CakePHP Event
     * @param Entity $entity Entity to be saved
     * @param ArrayObject $options Additional options
     * @return void
     */
    public function beforeSave(Event $event, EntityInterface $entity, \ArrayObject $options)
    {
        if (!$entity->isNew() && $entity->dirty()) {
            $fields = array_keys($entity->toArray());
            $dirtyFields = $entity->extract($fields, true);
            unset($dirtyFields['modified']);
            $this->_dirtyFields[$entity->id] = array_keys($dirtyFields);
        }
    }

    /**
     * afterSave Callback
     *
     * @param Event $event CakePHP Event
     * @param EntityInterface $entity Entity that was saved
     * @return void
     */
    public function afterSave(Event $event, EntityInterface $entity)
    {
        $action = $entity->isNew() ? ModelHistory::ACTION_CREATE : ModelHistory::ACTION_UPDATE;

        $dirtyFields = null;
        if ($action === ModelHistory::ACTION_UPDATE && isset($this->_dirtyFields[$entity->id])) {
            $dirtyFields = $this->_dirtyFields[$entity->id];
            unset($this->_dirtyFields[$entity->id]);
        }
        
        $this->ModelHistory->add($entity, $action, $this->_getUserId(), [
            'dirtyFields' => $dirtyFields
        ]);
        foreach ($this->config('customActions') as $customAction) {
            if ($customAction['status'] == $entity['status']) {
                $action = $customAction['action'];
                $this->ModelHistory->add($entity, $action, $this->_getUserId(), []);
            }
        }
    }

    /**
     * afterDelete Callback
     *
     * @param Event $event CakePHP Event
     * @param Entity $entity Entity that was deleted
     * @param ArrayObject $options Additional options
     * @return void
     */
    public function afterDelete(Event $event, EntityInterface $entity, \ArrayObject $options)
    {
        $this->ModelHistory->add($entity, ModelHistory::ACTION_DELETE, $this->_getUserId());
    }

    /**
     * Adds a comment to the model's history
     *
     * @param EntityInterface $entity Entity to add the comment to
     * @param string $comment Comment
     * @param string $userId Commenting User
     * @return ModelHistory
     */
    public function addCommentToHistory(EntityInterface $entity, $comment, $userId = null)
    {
        if (!$userId) {
            $userId = $this->_getUserId();
        }
        return $this->ModelHistory->addComment($entity, $comment, $userId);
    }

    /**
     * Tries to get a userId to use in the history from the given configuration
     *
     * @return string|null
     */
    protected function _getUserId()
    {
        $userId = null;
        $callback = $this->config('userIdCallback');
        if (is_callable($callback)) {
            $userId = $callback();
        }
        return $userId;
    }

    /**
     * Set a callback to get the user id
     *
     * @param callable $callback Callback which must return the user id
     * @return void
     */
    public function setModelHistoryUserIdCallback(callable $callback)
    {
        $this->config('userIdCallback', $callback);
    }

    /**
     * Get all custom action for current Table
     *
     * @return array
     */
    public function getCustomActions()
    {
        return $this->config('customActions');
    }

    /**
     * Get the user fields
     *
     * @return array
     */
    public function getUserNameFields()
    {
        return $this->config('userNameFields');
    }
}
