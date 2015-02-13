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
    protected $_defaultConfig = [];

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
        if ($entity->isNew()) {
            $entry = $this->ModelHistory->add($this->_table, $entity, ModelHistory::ACTION_CREATE);
        }
    }
}
