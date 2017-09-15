<?php
namespace ModelHistory\Model\Behavior;

use Cake\Core\Exception\Exception;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\Utility\Security;
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
            'firstname' => 'Users.firstname',
            'lastname' => 'Users.lastname',
            'id' => 'Users.id'
        ],
        'fields' => [],
        'associations' => [],
        'ignoreFields' => [
            'id',
            'created',
            'modified'
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
    public function initialize(array $config): void
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

        if (isset($config['ignoreFields'])) {
            $this->setConfig('ignoreFields', $config['ignoreFields'], false);
        }

        $singularUnderscoredTableName = Inflector::singularize(Inflector::underscore($this->_table->getAlias()));
        $allColumns = $this->_table->getSchema()->columns();

        foreach ($allColumns as $columnName) {
            if (in_array($columnName, $this->getConfig('ignoreFields'))) {
                continue;
            }

            $obfuscated = false;
            $searchable = true;
            $type = 'string';

            if (substr($columnName, -3) === '_id') {
                $singular = substr($columnName, 0, -3);
                $associationName = str_replace('_', '', Inflector::pluralize($singular));
                $association = $this->_table->association($associationName);
                if (!is_null($association)) {
                    switch ($association->type()) {
                        case $association::MANY_TO_MANY:
                            $type = 'association';
                            break;

                        case $association::ONE_TO_MANY:
                        case $association::MANY_TO_ONE:
                            $type = 'relation';
                            break;
                    }
                }
            } elseif (strpos($columnName, 'password') !== false) {
                $obfuscated = true;
                $searchable = false;
            } else {
                switch ($this->_table->getSchema()->columnType($columnName)) {
                    case 'boolean':
                        $type = 'bool';
                        break;
                    case 'json':
                        $type = 'hash';
                        break;
                    case 'integer':
                        $type = 'number';
                        break;
                    case 'datetime':
                        $type = 'date';
                        break;
                }
            }
            $translationIdentifier = $singularUnderscoredTableName . '.' . $columnName;

            $manualConfig = isset($config['fields'][$columnName]) ? $config['fields'][$columnName] : [];
            $columnConfig = Hash::merge([
                'name' => $columnName,
                'translation' => function () use ($translationIdentifier) {
                    return __($translationIdentifier);
                },
                'searchable' => $searchable,
                'saveable' => true,
                'obfuscated' => $obfuscated,
                'type' => $type
            ], $manualConfig);

            $this->setConfig('fields.' . $columnName, $columnConfig, false);
        }

        // fill the config array of fields not in the table schema with default values
        if (!empty($config['fields'])) {
            foreach ($config['fields'] as $columnName => $manualConfig) {
                if (!in_array($columnName, $allColumns)) {
                    if (empty($manualConfig['type'])) {
                        throw new Exception(
                            sprintf('ModelHistory config for field %s is missing a type.', $columnName)
                        );
                    }

                    $translationIdentifier = $singularUnderscoredTableName . '.' . $columnName;

                    $columnConfig = Hash::merge([
                        'name' => $columnName,
                        'translation' => function () use ($translationIdentifier) {
                            return __($translationIdentifier);
                        },
                        'searchable' => true,
                        'saveable' => true,
                        'obfuscated' => false,
                    ], $manualConfig);

                    $this->setConfig('fields.' . $columnName, $columnConfig, false);
                }
            }
        }

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
    public function beforeSave(Event $event, EntityInterface $entity, \ArrayObject $options): void
    {
        if (!$entity->isNew() && $entity->dirty()) {
            $saveHash = Security::hash(md5(uniqid()));
            if (empty($entity->save_hash)) {
                $entity->save_hash = $saveHash;
                $entity->dirty('save_hash', false);
            }

            $this->_dirtyFields[$entity->id] = $this->_extractDirtyFields($entity);
            $this->_applySaveHash($entity, $saveHash);
        }
    }

    /**
     * Extract dirty fields of given entity
     *
     * @return array
     */
    protected function _extractDirtyFields(EntityInterface $entity): array
    {
        $dirtyFields = [];
        $fields = array_keys($entity->toArray());
        $dirtyFields = $entity->extract($fields, true);
        unset($dirtyFields['modified']);

        return array_keys($dirtyFields);
    }

    /**
     * Apply save hash to entity
     *
     * @param  EntityInterface  $entity    Entity look for associated dirty fields
     * @param  string           $saveHash  Hash to identify save process
     * @return bool
     */
    protected function _applySaveHash(EntityInterface $entity, string $saveHash): bool
    {
        $output = [];
        if (defined('PHPUNIT_TESTSUITE')) {
            $associations = [
                'article',
                'article.article'
            ];
        } else {
            $associations = $this->getAssociations();
        }

        if (empty($associations)) {
            return false;
        }

        foreach ($associations as $assoc) {
            $object = $this->_recursivelyExtractObject($assoc, $entity);

            if ($object === null) {
                continue;
            }

            $object->save_hash = $saveHash;
            $object->dirty('save_hash', false);
        }

        return true;
    }

    /**
     * Recursively find object based on given dot-seperated string representing object properties.
     *
     * @param  string           $path    String path
     * @param  EntityInterface  $object  Object to use
     * @return null|object
     */
    protected function _recursivelyExtractObject(string $path, EntityInterface $object): ?EntityInterface
    {
        if (stripos($path, '.') !== false) {
            $split = explode('.', $path);
            $path = array_shift($split);

            if (count($split) > 0 && $object->{$path} !== null) {
                return $this->_recursivelyExtractObject(implode('.', $split), $object->{$path});
            }
        } else {
            return $object->{$path};
        }

        return null;
    }

    /**
     * afterSave Callback
     *
     * @param Event $event CakePHP Event
     * @param EntityInterface $entity Entity that was saved
     * @return void
     */
    public function afterSave(Event $event, EntityInterface $entity): void
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
    public function afterDelete(Event $event, EntityInterface $entity, \ArrayObject $options): void
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
    public function addCommentToHistory(EntityInterface $entity, string $comment, string $userId = null)
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
    protected function _getUserId(): ?string
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
    public function getRelationLink(string $fieldName, string $fieldValue = null): string
    {
        $tableName = Inflector::camelize(Inflector::pluralize(str_replace('_id', '', $fieldName)));

        $fieldConfig = $this->getConfig('fields.' . $fieldName);

        // reads the url defined for the given behavior (empty array if not defined)
        $fieldUrl = $this->getUrl();

        if (isset($fieldConfig['url'])) {
            // if url defined in fieldconfig. Overwrites default and behavior url config.
            $fieldUrl = $fieldConfig['url'];
        }

        unset($fieldUrl['controller']);

        $relationConfig = [
            'model' => $tableName,
            'bindingKey' => 'id',
            'url' => Hash::merge([
                'plugin' => null,
                'controller' => $tableName,
                'action' => 'view',
            ], $fieldUrl)
        ];

        $pass = [];
        if ($fieldValue !== null) {
            $pass = [$fieldValue];
        }

        try {
            $url = Router::url(Hash::merge($relationConfig['url'], $pass));
        } catch (Exception $e) {
            return $fieldValue;
        }

        return '<a href="' . $url . '" target="_blank">' . __(strtolower($tableName)) . '</a>';
    }

    /**
     * Set a callback to get the user id
     *
     * @param callable $callback Callback which must return the user id
     * @return void
     */
    public function setModelHistoryUserIdCallback(callable $callback): void
    {
        $this->config('userIdCallback', $callback);
    }

    /**
     * Get the user fields
     *
     * @param  bool $withoutModel set to true to only get the field names without model name
     * @return array
     */
    public function getUserNameFields(bool $withoutModel = false): array
    {
        $userNameFields = $this->config('userNameFields');
        if ($withoutModel) {
            foreach ($userNameFields as $key => $value) {
                $exploded = explode('.', $value);
                if (count($exploded) === 2) {
                    $userNameFields[$key] = $exploded[1];
                }
            }
        }

        return $userNameFields;
    }

    /**
     * Get count of entries to show.
     *
     * @return int
     */
    public function getEntriesLimit(): int
    {
        return $this->config('entriesToShow');
    }

    /**
     * Get fields config
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->getConfig('fields');
    }

    /**
     * Get url config for behavior
     *
     * @return array
     */
    public function getUrl(): array
    {
        if (is_array($this->config('url'))) {
            return $this->config('url');
        }

        return [];
    }

    /**
     * Get translated fields
     *
     * @return array
     */
    public function getTranslatedFields(): array
    {
        return Hash::apply($this->config('fields'), '{*}[searchable=true]', function ($array) {
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
    public function getSaveableFields(): array
    {
        return Hash::apply($this->config('fields'), '{*}[saveable=true]', function ($array) {
            $formatted = [];
            foreach ($array as $data) {
                $formatted[$data['name']] = $data;
            }

            return $formatted;
        });
    }

    /**
     * Retrieve associations to keep within this entry.
     *
     * @return array
     */
    public function getAssociations(): array
    {
        return $this->config('associations');
    }
}
