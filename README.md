# cake-model-history
CakePHP 3 Historization for database records

## including model-history in your project:
1. in the *initialize* function of the **Table** you want to use model-history in, insert the following lines of code:

```
$this->addBehavior('ModelHistory.Historizable', [
            'userNameFields' => [
                'firstname' => 'forename',
                'lastname' => 'surname',
                'id' => 'Users.id'
            ]
        ]);
```

Note that you might have to customize the above fields according to your **Users** table. If your fields aren't called 'forename' and 'surname', you must change them before you can use the behavior.

2. in the *initialize*  function of **AppController.php**, insert the following lines of code where needed:

```
$this->loadComponent('RequestHandler');
    TableRegistry::get('Users')->setModelHistoryUserIdCallback(function () {
        return $this->Auth->user('id');
    });
```

3. in your corresponding, relevant **Controller**s *initialize* function, be sure to call

```
parent::initialize();
```

4. add a new table in your database
add a new table to the database you're using. The new tables structure can be found in **schema.sql** which is located in src/vendor/codekanzlei/cake-model_history/config/
If you are using [SequelPro] (http://www.sequelpro.com/), you can use its *Query* function. Just paste the contents of **schema.sql** and run the query.