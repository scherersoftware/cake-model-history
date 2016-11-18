# CakePHP 3 cake-model-history

[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)

CakePHP 3 Historization for database records. Keeps track of changes performed by users and provides a customizable view element for displaying them.

## Requirements

- [scherersoftware cake-cktools](https://github.com/scherersoftware/cake-cktools) for JSON to MEDIUMBLOB type mapping
- [scherersoftware cake-frontend-bridge](https://github.com/scherersoftware/cake-frontend-bridge) for view elements and model history pagination via AJAX
- [Font Awesome](https://fortawesome.github.io/Font-Awesome/) panel navigation buttons

## Installation

#### 1. require the plugin in your `composer.json`
```
"require": {
	"codekanzlei/cake-model-history": "dev-master",
}
```

Open a terminal in your project-folder and run these commands:

	$ composer update

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
Run the following sql-quer on your project database. You can find it in the Plugin's `config/schema.sql` file.

```
CREATE TABLE `model_history` (
	`id` char(36) NOT NULL,
	`model` varchar(255) DEFAULT NULL COMMENT 'e.g. "Installation"',
	`foreign_key` char(36) DEFAULT NULL COMMENT 'uuid',
	`user_id` char(36) DEFAULT NULL,
	`action` varchar(45) DEFAULT NULL COMMENT 'e.g. "create", "update", "delete"',
	`data` mediumblob COMMENT 'JSON blob, schema per action',
	`revision` int(8) NOT NULL,
	`created` datetime DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

#### 4. AppController.php

**initialize()**

```
$this->loadComponent('RequestHandler');
TableRegistry::get('TableWithHistorizableBehavior')->setModelHistoryUserIdCallback(function () {
    return $this->Auth->user('id');
});
```
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

**Note:** By default, the model-history plugin matches changes to a database record to the user that performed and saved them by comparing table-fields 'firstname' and 'lastname' in `UsersTable` (See `$_defaultConfig` in `HistorizableBehavior.php` for these default settings). If your fields are not called 'forename' and 'surname', you can easily customize these settings according to the fieldnames in your UsersTable, like so:

```
$this->addBehavior('ModelHistory.Historizable', [
    'userNameFields' => [
        'firstname' => 'yourFirstName',
        'lastname' => 'yourLastName',
        'id' => 'Users.id'
    ],
    'obfuscatedFields' => [
        'password'
    ],
    'relations' => [
        'user_id' => [
            'model' => 'Users',
            'bindingKey' => 'id',
            'url' => [
                'plugin' => 'Admin',
                'controller' => 'Users',
                'action' => 'view',
                'addFieldAsPass' => true
            ]
        ]
    ]
]);
```

#### View setup
Use `ModelHistoryHelper.php` to create neat view elements containing a record's change history with one call in your view:

```
<?= $this->ModelHistory->modelHistoryArea($user); ?>
```

`modelHistoryArea` has the following **Options:**

- `commentBox` (false)

	Additionally renders an comment field (input type text). User input will be saved to the model_history table

- `panel` (false)

	Renders the model history as a view element with additional type 'panel', including a handy show/hide button

For the modelHistoryArea to fetch its data, add the 'ModelHistory' component to the baseComponents property in your Frontend.AppController under `/webroot/js/app/app_controller.js`.
If you haven't set up the FrontendBridge yet, follow [these steps](https://github.com/scherersoftware/cake-frontend-bridge). There you will also find a template for this file.

Make sure `app_controller.js` is loaded on the page where you want to show the modelHistoryArea.
Then the ModelHistory JS-Component will make AJAX requests to /model_history/ModelHistory/index/$modelName/$primaryKey according to the $entity you gave the helper method and populate the modelHistoryArea by itself.




```
'fields' => [
    [
        'name' => 'customer_number',
        'translation' => __('user.customer_number'),
        'searchable' => true,
        'type' => 'string', # string, bool, number, relation, date
        'displayParser' => null,
        'saveParser' => null
    ],
    [
        'name' => 'depot_number',
        'translation' => __('user.depot_number'),
        'searchable' => true,
        'type' => 'string', # string, bool, number, relation, date
        'displayParser' => null,
        'saveParser' => null
    ],
    [
        'name' => 'status',
        'translation' => __('user.status'),
        'searchable' => true,
        'type' => 'string',
        'displayParser' => null,
        'saveParser' => null
    ],
    [
        'name' => 'email',
        'translation' => __('user.email'),
        'searchable' => true,
        'type' => 'string',
        'displayParser' => null,
        'saveParser' => null
    ],
    [
        'name' => 'salutation',
        'translation' => __('user.salutation'),
        'searchable' => true,
        'type' => 'string',
        'displayParser' => null,
        'saveParser' => null
    ],
    [
        'name' => 'forename',
        'translation' => __('user.forename'),
        'searchable' => true,
        'type' => 'string', # string, bool, number, relation, date
        'displayParser' => function ($value) {
            return $value;
        },
        'saveParser' => function ($value) {
        }
    ],
    [
        'name' => 'surname',
        'translation' => __('user.surname'),
        'searchable' => true,
        'type' => 'string',
        'displayParser' => null,
        'saveParser' => null
    ],
    [
        'name' => 'citizenship',
        'translation' => __('user.citizenship'),
        'searchable' => true,
        'type' => 'string',
        'displayParser' => null,
        'saveParser' => null
    ],
    [
        'name' => 'family_status',
        'translation' => __('user.family_status'),
        'searchable' => true,
        'type' => 'string',
        'displayParser' => null,
        'saveParser' => null
    ],
    [
        'name' => 'birth_place',
        'translation' => __('user.birth_place'),
        'searchable' => true,
        'type' => 'string',
        'displayParser' => null,
        'saveParser' => null
    ],
    [
        'name' => 'birth_country',
        'translation' => __('user.birth_country'),
        'searchable' => true,
        'type' => 'string',
        'displayParser' => null,
        'saveParser' => null
    ],
    [
        'name' => 'legitimation_type',
        'translation' => __('user.legitimation_type'),
        'searchable' => true,
        'type' => 'string',
        'displayParser' => null,
        'saveParser' => null
    ],
    [
        'name' => 'legitimation_date',
        'translation' => __('user.legitimation_date'),
        'searchable' => true,
        'type' => 'date',
        'displayParser' => null,
        'saveParser' => function ($value) {
            if (!is_object($value)) {
                $value = new Time($value);
            }
            $value->setTimezone('Europe/Berlin');
            return $value;
        }
    ],
    [
        'name' => 'legitimation_authority',
        'translation' => __('user.legitimation_authority'),
        'searchable' => true,
        'type' => 'string',
        'displayParser' => null,
        'saveParser' => null
    ],
    [
        'name' => 'legal_form',
        'translation' => __('user.legal_form'),
        'searchable' => true,
        'type' => 'string',
        'displayParser' => null,
        'saveParser' => null
    ],
    [
        'name' => 'academic_title',
        'translation' => __('user.academic_title'),
        'searchable' => true,
        'type' => 'string',
        'displayParser' => null,
        'saveParser' => null
    ],
    [
        'name' => 'academic_grade',
        'translation' => __('user.academic_grade'),
        'searchable' => true,
        'type' => 'string',
        'displayParser' => null,
        'saveParser' => null
    ],
    [
        'name' => 'branches_id',
        'translation' => __('user.branches_id'),
        'searchable' => true,
        'type' => 'relation',
        'displayParser' => null,
        'saveParser' => null
    ],
    [
        'name' => 'deny_newsletter',
        'translation' => __('user.deny_newsletter'),
        'searchable' => true,
        'type' => 'string',
        'displayParser' => null,
        'saveParser' => null
    ],
]
```
