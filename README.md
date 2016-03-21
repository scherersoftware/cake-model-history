# CakePHP 3 cake-model-history
====================

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
    TableRegistry::get('Users')->setModelHistoryUserIdCallback(function () {
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
    ]
]);
```

#### View setup
Use `ModelHistoryHelper.php` to create neat view elements containg a record's change history with one call in your view:

```
<?= $this->ModelHistory->modelHistoryArea($user); ?>
```

`modelHistoryArea` has the following **Options:**

- `commentBox` (false)
	
	Additionally renders an comment field (input type text). User input will be saved to the model_history table

- `panel` (false)

	Renders the model history as a view element with additional type 'panel', includig a handy show/hide button
 
 	

## License

The MIT License (MIT)

Copyright (c) 2016 scherer software

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.