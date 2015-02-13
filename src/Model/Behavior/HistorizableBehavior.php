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
        'userIdCallback' => null
    ];

    /**
     * Instance of the ModelHistoryTable model
     *
     * @var ModelHistoryTable
     */
    public $ModelHistory;

    /**
     * Constructor hook method.
     *
     * @param array $config The configuration settings provided to this behavior.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->ModelHistory = TableRegistry::get('ModelHistory.ModelHistory');
        parent::initialize($config);
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
        $this->ModelHistory->add($this->_table, $entity, $action, $this->_getUserId());
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
        $this->ModelHistory->add($this->_table, $entity, ModelHistory::ACTION_DELETE, $this->_getUserId());
    }

    /**
     * Tries to the a userId to use in the history from the given configuration
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
}
