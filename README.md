![CakePHP 3 Model History Plugin](https://raw.githubusercontent.com/scherersoftware/cake-model-history/master/model-history.png)

[![Build Status](https://travis-ci.org/scherersoftware/cake-model-history.svg?branch=master)](https://travis-ci.org/scherersoftware/cake-model-history)
[![codecov](https://codecov.io/gh/scherersoftware/cake-model-history/branch/master/graph/badge.svg)](https://codecov.io/gh/scherersoftware/cake-model-history)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)

CakePHP 3 Historization for database records. Keeps track of changes performed by users and provides a customizable view element for displaying them.

## Requirements

- [scherersoftware cake-cktools](https://github.com/scherersoftware/cake-cktools) for JSON to MEDIUMBLOB type mapping
- [Font Awesome](https://fortawesome.github.io/Font-Awesome/) panel navigation buttons

## Installation

#### 1. require the plugin in your `composer.json`

```
"require": {
    "codekanzlei/cake-model-history": "2.0.*",
}
```

Open a terminal in your project-folder and run these commands:

    `$ composer update`

#### 2. Configure `config/bootstrap.php`

**Load** the Plugin:

```
Plugin::load('ModelHistory', ['bootstrap' => false, 'routes' => true]);
```

Since all changes to a record are saved to the field `data` (type `MEDIUMBLOB`) in the `ModelHistoryTable` in JSON format, you must use custom **Type Mapping**.

```
Type::map('json', 'CkTools\Database\Type\JsonType');
```


#### 3. Create a table `model_history` in your project database
We have to create the database schema with help of the migrations plugin.

```
    $ bin/cake migrations migrate -p ModelHistory
```

#### 4. AppController.php

**$helpers**

```
public $helpers =  [
    'ModelHistory.ModelHistory'
]
```


## Usage & Configuration:

#### Table setup
Add the Historizable Behavior in the `initialize` function of the **Table** you want to use model-history.

```
$this->addBehavior('ModelHistory.Historizable');
```

**Note:** By default, the model-history plugin attributes changes to a database record to the user that performed and saved them by comparing table-fields 'firstname' and 'lastname' in `UsersTable` (See `$_defaultConfig` in `HistorizableBehavior.php` for these default settings). If your fields are not called 'firstname' and 'lastname', you can easily customize these settings according to the fieldnames in your UsersTable, like so:

```
$this->addBehavior('ModelHistory.Historizable', [
    'userNameFields' => [
        'firstname' => 'User.your_first_name_field',
        'lastname' => 'Users.your_last_name_field',
        'id' => 'Users.id'
    ],
    'userIdCallback' => null,
    'entriesToShow' => 10,
    'fields' => [],
    'associations' => [],
    'ignoreFields' => []
]);
```

By default, the ModelHistory monitors changes for every field it finds in the schema of the table.
It tries to deduct the type to use from the data type and obfuscates all fields containing "password".
Otherwise, the default values are `'searchable' => true`, `'saveable' => true` and `'obfuscated' => false`.

To override specific settings, add an array for the field into the 'fields' array and list the values you want to override.

```
$this->addBehavior('ModelHistory.Historizable', [
    'fields' => [
    // The field name
        'firstname' =>[
            // Allowed translation forms: String, Closure returning string
            // Its recommended to use the closure, so translations are made after core initialize when the correct language was set.
            'translation' => function () {
                return __('user.firstname');
            },
            // The searchable indicator is used to show the field in the filter box
            // defaults to true
            'searchable' => true,
            // The savable indicator is used to decide whether the field is tracked
            // defaults to true
            'saveable' => true,
            // obfuscate the values of the field in the history view.
            // defaults to false except for fieldnames containing "password"
            'obfuscated' => false,
            // Allowed: string, bool, number, relation, date, hash, array, association, mass_association.
            'type' => 'string',
            // Optional display parser to modify the value before displaying it,
            // if no displayParser is found, the \ModelHistory\Model\Transform\{$type}Transformer is used.
            'displayParser' => function ($fieldname, $value, $entity) {
                return $value;
            },
            // Optional save parser to modify the value before saving the history entry
            'saveParser' => function ($fieldname, $value, $entity) {
                return $value;
            },
        ],
    ]
]);
```

Because the default monitored fields are limited to those in the table, n:m associations and associations
with foreign_keys in other tables always have to be configured, at least with the type.
Here are three real world examples from a UsersTable:

```
$this->addBehavior('ModelHistory.Historizable', [
    'fields' => [
        'customer_id' => [
            'saveable' => false,
            'type' => 'association'
        ],
        'regions' => [
            'type' => 'mass_association',
            'displayParser' => function ($fieldName, $value, $entity) {
                if (is_array($value)) {
                    return implode(', ', $value);
                }

                return $value;
            }
        ],
        'contact' => [
            'type' => 'string',
            'saveParser' => function ($fieldName, $value, $entity) {
                return __('user.associated_contact');
            },
            'displayParser' => function ($fieldName, $value, $entity) {
                return __('user.associated_contact');
            }
        ],
    ]
]);
```

**Types:**

- `string`: for string values.
- `bool`: for bool values.
- `number`: for integer values.
- `date`: for date values.
- `hash`: for associative arrays.
- `array`: for sequential (indexed) arrays.
- `relation`: for 1 to n relations.
- `association`: for n to m relations.
- `mass_association`: for n to m relations which shall be condensed into one history entry.

The three types `relation`, `association` and `mass_association` are able to link to associated entities. The url defaults to:

```
'url' => [
    'plugin' => null,
    'controller' => $entityController // automatically set to the entities controller
    'action' => 'view'
]
```

The default url can be overwritten: `url` in the behavior-array overwrites default and the defined url in the field-config has the highest priority.

```
$this->addBehavior('ModelHistory.Historizable', [
    'url' => [
        'plugin' => 'Admin',
        'action' => 'index'
    ],
    'fields' => [
        [
            'name' => 'firstname',
            'translation' => function () {
                return __('user.firstname');
            },
            'searchable' => true,
            'saveable' => true,
            'obfuscated' => false,
            'type' => 'relation',
            'url' => [
                'plugin' => 'Special',
                'action' => 'show'
            ]
        ],
    ]
]);
```

If saved association's entries shall be viewed within the source entities entries, you can specify the `associations` key. It has to match the object property keys given in the
source entity. Furthermore the model history area has to get the option `includeAssociated` with value `true`.

```
$this->addBehavior('ModelHistory.Historizable', [
    'fields' => [
        ...
    ],
    'associations' => [
        'contact',
        'contact.address'
    ]
]);
```

To further specify the context in which the entity was saved and in order to gather additional information, you can implement `\ModelHistory\Model\Entity\HistoryContextTrait` inside your entity. You have to call `setHistoryContext` on the entity to add the context information.
Currently there are three context types: `ModelHistory::CONTEXT_TYPE_CONTROLLER`, `ModelHistory::CONTEXT_TYPE_SHELL` and `ModelHistory::CONTEXT_TYPE_SLUG`.
The optional slug gets translated automatically, when its defined in the entities `typeDescriptions`.

```
    /**
     * Index action of a controller
     */
    public function index()
    {
        if ($this->request->is(['post'])) {
            $entity = $this->Table->newEntity($this->request->data);
            $entity->setHistoryContext(ModelHistory::CONTEXT_TYPE_CONTROLLER, $this->request, 'optional_slug');
            $this->Table->save($entity);
        }
    }

```

You can also specify a context getter inside an entity to search for defined contexts. Please keep in mind that you have to use the `TypeAwareTrait` from the `CkTools`:

```
    use \CkTools\Utility\TypeAwareTrait;

    /**
     * Retrieve defined contexts
     *
     * @return void
     */
    public static function getContexts()
    {
        return self::getTypeMap(
            self::CONTEXT_TYPE_FORGOT_PASSWORD
        );
    }
```


**Ignoring fields**

By default, the fields `id`, `created` and `modified` are not tracked.
If you want to overwrite which fields are ignored or not, give `ignoreFields` in the config array and only those fields will be ignored:

```
$this->addBehavior('ModelHistory.Historizable', [
    'ignoreFields' => [
        'secret_field',
        'created'
    ]
]);
```

#### View setup
Use `ModelHistoryHelper.php` to create neat view elements containing a record's change history with one call in your view:

```
<?= $this->ModelHistory->modelHistoryArea($user); ?>
```

`modelHistoryArea` has the following **Options:**

- `showCommentBox` (false)

    Additionally renders an comment field (input type text). User input will be saved to the model_history table

- `showFilterBox` (false)

    Additionally renders a filter box which can be used to search for entries.

- `includeAssociated` (false)

    Additionally include all associations saved.

For the modelHistoryArea to fetch its data, add the 'ModelHistory' component to the baseComponents property in your Frontend.AppController under `/webroot/js/app/app_controller.js`.
If you haven't set up the FrontendBridge yet, follow [these steps](https://github.com/scherersoftware/cake-frontend-bridge). There you will also find a template for this file.

Make sure `app_controller.js` is loaded on the page where you want to show the modelHistoryArea.
Then the ModelHistory JS-Component will make AJAX requests to /model_history/ModelHistory/index/$modelName/$primaryKey according to the $entity you gave the helper method and populate the modelHistoryArea by itself.
