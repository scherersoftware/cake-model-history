<?php
namespace ModelHistory\Test\TestCase\Model\Table;

use App\Lib\Status;
use Cake\Database\Driver;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use ModelHistoryTestApp\Table\ArticlesTable;
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

        if ($this->Articles->hasBehavior('Historizable')) {
            $this->Articles->removeBehavior('Historizable');
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
        parent::tearDown();
    }

    protected function _getBehaviorConfig($callback = null) {
        return [
            'userIdCallback' => $callback,
            'fields' => [
                [
                    'name' => 'id',
                    'translation' => __('articles.id'),
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'string',
                    'displayParser' => null,
                    'saveParser' => null
                ],
                [
                    'name' => 'title',
                    'translation' => __('articles.title'),
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'string',
                    'displayParser' => null,
                    'saveParser' => null
                ],
                [
                    'name' => 'status',
                    'translation' => __('articles.status'),
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'string',
                    'displayParser' => null,
                    'saveParser' => null
                ],
                [
                    'name' => 'content',
                    'translation' => __('articles.content'),
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'string',
                    'displayParser' => null,
                    'saveParser' => null
                ],
                [
                    'name' => 'json_field',
                    'translation' => __('articles.json_field'),
                    'searchable' => false,
                    'saveable' => false,
                    'obfuscated' => false,
                    'type' => 'string',
                    'displayParser' => null,
                    'saveParser' => null
                ],
                [
                    'name' => 'int_field',
                    'translation' => __('articles.int_field'),
                    'searchable' => false,
                    'saveable' => false,
                    'obfuscated' => false,
                    'type' => 'string',
                    'displayParser' => null,
                    'saveParser' => null
                ]
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
            'title' => 'foobar'
        ]);
        $this->Articles->save($article);

        $modelHistoriesCount = $this->ModelHistory->find()
            ->where([
                'model' => 'Articles',
                'foreign_key' => $article->id,
                'action' => ModelHistory::ACTION_CREATE
            ])->count();
        $this->assertEquals($modelHistoriesCount, 1);

        $entry = $this->ModelHistory->find()->first();

        $this->assertTrue(is_array($entry->data));
        $this->assertEquals($entry->data['id'], $article->id);
        $this->assertEquals($entry->data['title'], $article->title);

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
        $this->Articles->addBehavior('ModelHistory.Historizable', $this->_getBehaviorConfig(function () use ($userId) {
            return $userId;
        }));

        $article = $this->Articles->newEntity([
            'title' => 'foobar',
            'content' => 'lorem'
        ]);
        $this->Articles->save($article);

        $entityWithHistory = $this->ModelHistory->getEntityWithHistory('Articles', $article->id, [], ['className' => 'ModelHistoryTestApp\Model\Table\ArticlesTable']);

        $this->assertInstanceOf('ModelHistoryTestApp\Model\Entity\Article', $entityWithHistory);
        $this->assertNotEmpty($entityWithHistory->model_history);
        $this->assertEquals(1, count($entityWithHistory->model_history));

        foreach ($entityWithHistory->model_history as $historyEntry) {
            $this->assertEquals($historyEntry->foreign_key, $article->id);
        }
    }
}
