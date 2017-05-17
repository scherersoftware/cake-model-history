<?php
namespace ModelHistory\Test\TestCase\Model\Table;

use App\Lib\Status;
use Cake\Database\Driver;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use ModelHistoryTestApp\Table\ArticlesTable;
use ModelHistoryTestApp\Table\ArticlesUsersTable;
use ModelHistory\Model\Entity\ModelHistory;
use ModelHistory\Model\Table\ModelHistoryTable;

/**
 * ModelHistory\Model\Table\ModelHistoryTable Test Case
 */
class ModelHistoryTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'ModelHistory' => 'plugin.model_history.model_history',
        'Articles' => 'plugin.model_history.articles',
        'Users' => 'plugin.model_history.users',
        'ArticlesUsers' => 'plugin.model_history.articles_users',
        'Items' => 'plugin.model_history.items',
        'ArticlesItems' => 'plugin.model_history.articles_items',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        TableRegistry::clear();

        $this->ModelHistory = TableRegistry::get('ModelHistory', ['className' => 'ModelHistory\Model\Table\ModelHistoryTable']);
        $this->Articles = TableRegistry::get('ArticlesTable', ['className' => 'ModelHistoryTestApp\Model\Table\ArticlesTable']);
        $this->ArticlesUsers = TableRegistry::get('ArticlesUsersTable', ['className' => 'ModelHistoryTestApp\Model\Table\ArticlesUsersTable']);
        $this->Items = TableRegistry::get('ItemsTable', ['className' => 'ModelHistoryTestApp\Model\Table\ItemsTable']);

        if ($this->Articles->hasBehavior('Historizable')) {
            $this->Articles->removeBehavior('Historizable');
        }
        if ($this->ArticlesUsers->hasBehavior('Historizable')) {
            $this->ArticlesUsers->removeBehavior('Historizable');
        }
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ModelHistory);
        unset($this->Articles);
        unset($this->ArticlesUsers);
        unset($this->Items);
        parent::tearDown();
    }

    protected function _getBehaviorConfig($callback = null)
    {
        return [
            'userIdCallback' => $callback,
            'fields' => [
                [
                    'name' => 'id',
                    'translation' => function () {
                        return __('articles.id');
                    },
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'string'
                ],
                [
                    'name' => 'title',
                    'translation' => function () {
                        return __('articles.title');
                    },
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'string',
                    'displayParser' => function ($fieldname, $value, $entity) {
                        return $value;
                    },
                    'saveParser' => function ($fieldname, $value, $entity) {
                        return $value;
                    }
                ],
                [
                    'name' => 'status',
                    'translation' => function () {
                        return __('articles.status');
                    },
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'string'
                ],
                [
                    'name' => 'content',
                    'translation' => function () {
                        return __('articles.content');
                    },
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'string'
                ],
                [
                    'name' => 'json_field',
                    'translation' => function () {
                        return __('articles.json_field');
                    },
                    'searchable' => false,
                    'saveable' => false,
                    'obfuscated' => false,
                    'type' => 'string'
                ],
                [
                    'name' => 'users',
                    'translation' => function () {
                        return __('articles.mass_assoc_field');
                    },
                    'searchable' => false,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'mass_association'
                ],
                [
                    'name' => 'int_field',
                    'translation' => function () {
                        return __('articles.int_field');
                    },
                    'searchable' => false,
                    'saveable' => false,
                    'obfuscated' => true,
                    'type' => 'string'
                ],
                [
                    'name' => 'user_id',
                    'translation' => function () {
                        return __('articles.assoc_field');
                    },
                    'searchable' => false,
                    'saveable' => false,
                    'obfuscated' => false,
                    'type' => 'association'
                ],
            ]
        ];
    }

    /**
     * Test create, update, delete
     *
     * @return void
     */
    public function testBasicCreateAndUpdateAndDelete()
    {
        $this->Articles->addBehavior('ModelHistory.Historizable', $this->_getBehaviorConfig());
        $article = $this->Articles->newEntity([
            'title' => 'foobar',
            'status' => 'barfoo'
        ]);
        $article->hiddenProperties(['status']);
        $this->Articles->save($article);

        $modelHistoryCount = $this->ModelHistory->getModelHistoryCount('Articles', $article->id);

        $this->assertEquals($modelHistoryCount, 1);

        $entry = $this->ModelHistory->find()->first();

        $this->assertTrue(is_array($entry->data));
        $this->assertEquals($entry->data['id'], $article->id);
        $this->assertEquals($entry->data['title'], $article->title);
        $this->assertEquals($entry->data['status'], $article->status);

        $article->title = 'changed';
        $this->Articles->save($article);

        $entry = $this->ModelHistory->find()
            ->where([
                'model' => 'Articles',
                'foreign_key' => $article->id,
                'action' => ModelHistory::ACTION_UPDATE
            ])
            ->first();

        $this->assertEquals($entry->data['title'], 'changed');

        $this->Articles->delete($article);
        $entry = $this->ModelHistory->find()
            ->where([
                'model' => 'Articles',
                'foreign_key' => $article->id,
                'action' => ModelHistory::ACTION_DELETE
            ])
            ->first();

        $this->assertTrue(empty($entry->data));
    }

    /**
     * Test passing a callable for ModelHistory to fetch the user id
     *
     * @return void
     */
    public function testPassUserIdCallbackWithBehaviorConfig()
    {
        $userId = '481fc6d0-b920-43e0-a40d-6d1740cf8562';
        $this->Articles->addBehavior('ModelHistory.Historizable', $this->_getBehaviorConfig(function () use ($userId) {
            return $userId;
        }));
        $article = $this->Articles->newEntity([
            'title' => 'foobar'
        ]);
        $this->Articles->save($article);

        $entry = $this->ModelHistory->find()->first();
        $this->assertEquals($entry->user_id, $userId);
    }

    /**
     * Test passing a callable for ModelHistory to fetch the user id
     *
     * @return void
     */
    public function testPassUserIdCallbackWithMethod()
    {
        $userId = '481fc6d0-b920-43e0-a40d-6d1740cf8562';
        $callback = function () use ($userId) {
            return $userId;
        };

        $this->Articles->addBehavior('ModelHistory.Historizable', $this->_getBehaviorConfig());
        $this->Articles->setModelHistoryUserIdCallback($callback);

        $article = $this->Articles->newEntity([
            'title' => 'foobar'
        ]);
        $this->Articles->save($article);

        $entry = $this->ModelHistory->find()->first();
        $this->assertEquals($entry->user_id, $userId);
    }

    /**
     * Test that the sequence number is incremented correctly
     *
     * @return void
     */
    public function testModelHistoryRevision()
    {
        $this->Articles->addBehavior('ModelHistory.Historizable', $this->_getBehaviorConfig());
        $article = $this->Articles->newEntity([
            'title' => 'foobar'
        ]);
        $this->Articles->save($article);

        $entry = $this->ModelHistory->find()->first();
        $this->assertEquals($entry->revision, 1);

        $article->title = 'changed';
        $this->Articles->save($article);

        $entry = $this->ModelHistory->find()
            ->where([
                'model' => 'Articles',
                'foreign_key' => $article->id,
                'action' => ModelHistory::ACTION_UPDATE
            ])
            ->first();
        $this->assertEquals($entry->revision, 2);

        $article->title = 'changed again';
        $this->Articles->save($article);

        $entry = $this->ModelHistory->find()
            ->where([
                'model' => 'Articles',
                'foreign_key' => $article->id,
                'action' => ModelHistory::ACTION_UPDATE
            ])
            ->order(['revision DESC'])
            ->first();
        $this->assertEquals($entry->revision, 3);
    }

    /**
     * Test that only the diff is saved in updates
     *
     * @return void
     */
    public function testDataDiff()
    {
        $this->Articles->addBehavior('ModelHistory.Historizable', $this->_getBehaviorConfig());
        $article = $this->Articles->newEntity([
            'title' => 'foobar',
            'content' => 'lorem'
        ]);
        $this->Articles->save($article);

        $article->title = 'changed';
        $this->Articles->save($article);

        $entry = $this->ModelHistory->find()
            ->where([
                'model' => 'Articles',
                'foreign_key' => $article->id,
                'action' => ModelHistory::ACTION_UPDATE
            ])
            ->first();

        // make sure only the title field is persisted in the data field
        $this->assertEquals($entry->data, [
            'title' => 'changed'
        ]);
    }

    /**
     * Test adding and fetching model history comments
     *
     * @return void
     */
    public function testCommenting()
    {
        $userId = '481fc6d0-b920-43e0-a40d-6d1740cf8562';
        $comment = 'foo bar baz';

        $this->Articles->addBehavior('ModelHistory.Historizable', $this->_getBehaviorConfig());
        $article = $this->Articles->newEntity([
            'title' => 'foobar',
            'content' => 'lorem'
        ]);
        $this->Articles->save($article);

        $h = $this->Articles->addCommentToHistory($article, $comment, $userId);

        $entry = $this->ModelHistory->find()
            ->where([
                'model' => 'Articles',
                'foreign_key' => $article->id,
                'action' => ModelHistory::ACTION_COMMENT
            ])
            ->first();

        $this->assertEquals($entry->user_id, $userId);
        $this->assertEquals($entry->data['comment'], $comment);
    }

    /**
     * Test searchable flag
     *
     * @return void
     */
    public function testSearchableFields()
    {
        $this->Articles->addBehavior('ModelHistory.Historizable', $this->_getBehaviorConfig());
        $fields = $this->Articles->getFields();

        foreach ($this->Articles->getTranslatedFields() as $fieldname => $translation) {
            $searchable = $fields[$fieldname]['searchable'];
            $this->assertEquals($searchable, true);

            unset($fields[$fieldname]);
        }

        foreach ($fields as $fieldname => $data) {
            $this->assertEquals($data['searchable'], false);
        }
    }

    /**
     * Test searchable flag
     *
     * @return void
     */
    public function testSaveableFields()
    {
        $this->Articles->addBehavior('ModelHistory.Historizable', $this->_getBehaviorConfig());
        $fields = $this->Articles->getFields();

        foreach ($this->Articles->getSaveableFields() as $fieldname => $data) {
            $saveable = $data['saveable'];
            $this->assertEquals($saveable, true);

            unset($fields[$fieldname]);
        }

        foreach ($fields as $fieldname => $data) {
            $this->assertEquals($data['saveable'], false);
        }
    }

    /**
     * Test entity with contained history getter
     *
     * @return void
     */
    public function testGetEntityWithHistory()
    {
        $userId = '481fc6d0-b920-43e0-a40d-6d1740cf8562';
        $this->Articles->addBehavior('ModelHistory.Historizable', $this->_getBehaviorConfig());

        $article = $this->Articles->newEntity([
            'title' => 'foobar',
            'content' => 'lorem'
        ]);
        $this->Articles->save($article);

        $entityWithHistory = $this->ModelHistory->getEntityWithHistory('Articles', $article->id);

        $this->assertInstanceOf('ModelHistoryTestApp\Model\Entity\Article', $entityWithHistory);
        $this->assertNotEmpty($entityWithHistory->model_history);
        $this->assertEquals(1, count($entityWithHistory->model_history));

        foreach ($entityWithHistory->model_history as $historyEntry) {
            $this->assertEquals($historyEntry->foreign_key, $article->id);
        }
    }

    public function testGetModelHistory()
    {
        $userId = '481fc6d0-b920-43e0-a40d-6d1740cf8562';
        $this->Articles->addBehavior('ModelHistory.Historizable', [
            'userIdCallback' => function () use ($userId) {
                return $userId;
            },
            'fields' => [
                [
                    'name' => 'id',
                    'translation' => function () {
                        return __('articles.id');
                    },
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'string'
                ],
                [
                    'name' => 'title',
                    'translation' => function () {
                        return __('articles.title');
                    },
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'string',
                    'displayParser' => function ($fieldname, $value, $entity) {
                        return $value;
                    }
                ],
                [
                    'name' => 'status',
                    'translation' => function () {
                        return __('articles.status');
                    },
                    'searchable' => false,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'string'
                ],
                [
                    'name' => 'content',
                    'translation' => function () {
                        return __('articles.content');
                    },
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'string'
                ],
            ]
        ]);

        $article = $this->Articles->newEntity([
            'title' => 'foobar',
            'content' => 'lorem'
        ]);
        $this->Articles->save($article);

        $modelHistory = $this->ModelHistory->getModelHistory('Articles', $article->id, 10, 1, [], ['includeAssociated' => true]);
        $modelHistoryCount = $this->ModelHistory->getModelHistoryCount('Articles', $article->id);

        $this->assertEquals(1, $modelHistoryCount);
    }

    /**
     * Test building of the diff
     *
     * @return void
     */
    public function testBuildDiff()
    {
        $userId = '481fc6d0-b920-43e0-a40d-6d1740cf8562';
        $this->Articles->addBehavior('ModelHistory.Historizable', $this->_getBehaviorConfig());

        $article = $this->Articles->newEntity([
            'title' => 'foobar',
            'content' => 'lorem',
            'status' => 'yes'
        ]);
        $this->Articles->save($article);

        $modelHistory = $this->ModelHistory->getModelHistory('Articles', $article->id, 1000, 1);
        $this->assertEquals(1, count($modelHistory));

        $diff = $this->ModelHistory->buildDiff($modelHistory[0]);
        $this->assertTrue(empty($diff));

        $article = $this->Articles->patchEntity($article, [
            'title' => 'bar',
            'content' => 'ipsum'
        ]);
        $this->Articles->save($article);

        $modelHistory = $this->ModelHistory->getModelHistory('Articles', $article->id, 1000, 1);
        $this->assertEquals(2, count($modelHistory));

        $article = $this->Articles->get($article->id);
        $article = $this->Articles->patchEntity($article, [
            'status' => 'woot'
        ]);
        $this->Articles->save($article);

        $modelHistory = $this->ModelHistory->getModelHistory('Articles', $article->id, 1000, 1);
        $this->assertEquals(3, count($modelHistory));

        $i = count($modelHistory);
        foreach ($modelHistory as $historyEntry) {
            $this->assertEquals($i, $historyEntry->revision);

            $diff = $this->ModelHistory->buildDiff($historyEntry);

            switch ($i) {
                case 3:
                    $this->assertNotEmpty($diff);
                    $this->assertNotEmpty($diff['changed']);
                    $this->assertNotEmpty($diff['changedBefore']);
                    $this->assertNotEmpty($diff['unchanged']);
                    $this->assertArrayHasKey('status', $diff['changed']);
                    $this->assertArrayHasKey('title', $diff['changedBefore']);
                    $this->assertArrayHasKey('content', $diff['changedBefore']);
                    $this->assertArrayHasKey('id', $diff['unchanged']);
                    break;
                case 2:
                    $this->assertNotEmpty($diff);
                    $this->assertNotEmpty($diff['changed']);
                    $this->assertNotEmpty($diff['changedBefore']);
                    $this->assertNotEmpty($diff['unchanged']);
                    $this->assertArrayHasKey('title', $diff['changed']);
                    $this->assertArrayHasKey('content', $diff['changed']);
                    $this->assertArrayHasKey('status', $diff['changedBefore']);
                    $this->assertArrayHasKey('id', $diff['unchanged']);
                    break;
                case 1:
                    $this->assertEmpty($diff);
                    break;
            }
            $i--;
        }
    }

    /**
     * Test create, update, delete
     *
     * @return void
     */
    public function testSaveAssociation()
    {
        $articleUser = $this->ArticlesUsers->addBehavior('ModelHistory.Historizable', [
            'userIdCallback' => null,
            'fields' => [
                [
                    'name' => 'article_id',
                    'translation' => function () {
                        return __('articles_users.article');
                    },
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'association',
                    'associationKey' => 'user_id'
                ],
                [
                    'name' => 'user_id',
                    'translation' => function () {
                        return __('articles_users.user');
                    },
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'association',
                    'associationKey' => 'article_id'
                ]
            ]
        ]);
        $articleUser = $this->ArticlesUsers->newEntity([
            'article_id' => '7997df22-ed8e-4703-b971-d9514179904b',
            'user_id' => 'e5fba0df-33cf-46dc-9940-5f16382a9bd3'
        ]);

        $this->ArticlesUsers->save($articleUser);
        $modelHistoriesCount = $this->ModelHistory->find()
            ->where([
                'model' => 'Articles',
                'foreign_key' => $articleUser->article_id,
                'action' => ModelHistory::ACTION_CREATE
            ])->count();
        $this->assertEquals($modelHistoriesCount, 1);
        $modelHistoriesCount = $this->ModelHistory->find()
            ->where([
                'model' => 'Users',
                'foreign_key' => $articleUser->user_id,
                'action' => ModelHistory::ACTION_CREATE
            ])->count();
        $this->assertEquals($modelHistoriesCount, 1);
    }

    /**
     * Test create, update, delete
     *
     * @return void
     */
    public function testMassAssociation()
    {
        $itemData = [
            'name' => 'foobar',
            'articles' => [
                ['id' => '7997df22-ed8e-4703-b971-d9514179904b'],
                ['id' => 'd744e525-2957-4b28-a0ac-d5ffecb74485'],
                ['id' => '80cd952f-f410-4e25-9323-11922e90ee0b']
            ]
        ];
        $item = $this->Items->newEntity();
        $item = $this->Items->patchEntity($item, $itemData, [
            'associated' => [
                'Articles'
            ]
        ]);
        $this->Items->save($item, [
            'associated' => [
                'Articles'
            ]
        ]);
        $modelHistoriesCount = $this->ModelHistory->find()
            ->where([
                'model' => 'Items',
                'foreign_key' => $item->id,
                'action' => ModelHistory::ACTION_CREATE
            ])->count();

        $this->assertEquals($modelHistoriesCount, 1);

        $entry = $this->ModelHistory->find()->first();

        $this->assertInstanceOf('ModelHistory\Model\Entity\ModelHistory', $entry);
        $this->assertTrue(is_array($entry->data));
        $this->assertEquals($entry->foreign_key, $item->id);
        $this->assertArrayHasKey('articles', $entry->data);
        $this->assertEquals(count($entry->data['articles']), 3);

        $modelHistory = $this->ModelHistory->getModelHistory('Items', $item->id, 10, 1);
        $this->assertEquals(1, count($modelHistory));

        $data = explode(',', $modelHistory[0]->data['articles']);
        $this->assertEquals(3, count($data));
    }
}
