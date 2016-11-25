<?php
namespace ModelHistory\Model\Behavior;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
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
        'entriesToShow' => 10,
        'userNameFields' => [
            'firstname' => 'Users.forename',
            'lastname' => 'Users.surname',
            'id' => 'Users.id'
        ],
        'fields' => []
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
        // Set default translations
        $this->config('translations', [
            'id' => __d('model_history', 'field.id'),
            'comment' => __d('model_history', 'field.comment'),
            'created' => __d('model_history', 'field.created'),
            'modified' => __d('model_history', 'field.modified')
        ]);

        $this->ModelHistory = TableRegistry::get('ModelHistory.ModelHistory');
        // Dynamically attach the hasMany relationship
        $this->_table->hasMany('ModelHistory.ModelHistory', [
            'conditions' => [
                'ModelHistory.model' => $this->_table->registryAlias()
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
    }

    /**
     * afterDelete Callback
     *
     * @param Event $event CakePHP Event
     * @param Entity $entity Entity that was deleted
     * @param ArrayObject $options Additional options
     * @return void
     */
    public function afterDelete(Event $event, EntityInterface $entity, $options)
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
     * Get <a /> element for given ID Field
     *
     * @param  string  $fieldName   Fieldname
     * @param  string  $fieldValue  Value
     * @return string
     */
    public function getRelationLink($fieldName, $fieldValue = null)
    {
        $tableName = Inflector::camelize(Inflector::pluralize(str_replace('_id', '', $fieldName)));
        $relationConfig = [
            'model' => $tableName,
            'bindingKey' => 'id',
            'url' => [
                'plugin' => 'Admin',
                'controller' => $tableName,
                'action' => 'view',
                'addFieldAsPass' => true
            ]
        ];

        $pass = [];
        if (isset($relationConfig['url']['addFieldAsPass']) && $relationConfig['url']['addFieldAsPass'] === true) {
            $pass = [$fieldValue];
        }
        unset($relationConfig['url']['addFieldAsPass']);

        if (is_array($relationConfig['url'])) {
            try {
                $url = Router::url(Hash::merge($relationConfig['url'], $pass));
            } catch (\Cake\Core\Exception\Exception $e) {
                return $fieldValue;
            }
        }

        return '<a href="' . $url . '" target="_blank">' . __(strtolower($tableName)) . '</a>';
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
     * Get the user fields
     *
     * @return array
     */
    public function getUserNameFields()
    {
        return $this->config('userNameFields');
    }

    /**
     * Get count of entries to show.
     *
     * @return int
     */
    public function getEntriesLimit()
    {
        return $this->config('entriesToShow');
    }

    /**
     * Get fields config
     *
     * @return array
     */
    public function getFields()
    {
        return Hash::apply($this->config('fields'), '{n}', function ($array) {
            $output = [];
            foreach ($array as $data) {
                $output[$data['name']] = $data;
            }

            return $output;
        });
    }

    /**
     * Get translated fields
     *
     * @return array
     */
    public function getTranslatedFields()
    {
        return Hash::apply($this->config('fields'), '{n}[searchable=true]', function ($array) {
            $formatted = [];
            foreach ($array as $data) {
                $formatted[$data['name']] = $data['translation'];
            }

            return Hash::sort($formatted, '{s}', 'asc');
        });
    }

    /**
     * Get saveable fields
     *
     * @return array
     */
    public function getSaveableFields()
    {
        return Hash::apply($this->config('fields'), '{n}[saveable=true]', function ($array) {
            $formatted = [];
            foreach ($array as $data) {
                $formatted[$data['name']] = $data;
            }

            return $formatted;
        });
    }
}
