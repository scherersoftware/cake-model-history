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

    /**
     * Test create, update, delete
     *
     * @return void
     */
    public function testBasicCreateAndUpdateAndDelete()
    {
        $this->Articles->addBehavior('ModelHistory.Historizable');
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
        $callback = function () use ($userId) {
            return $userId;
        };

        $this->Articles->addBehavior('ModelHistory.Historizable', [
            'userIdCallback' => $callback
        ]);

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
        
        $this->Articles->addBehavior('ModelHistory.Historizable');
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
        $this->Articles->addBehavior('ModelHistory.Historizable');
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
        $this->Articles->addBehavior('ModelHistory.Historizable');
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

        $this->Articles->addBehavior('ModelHistory.Historizable');
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
     * undocumented function
     *
     * @return void
     */
    public function testCustomActions()
    {
        $this->Articles->addBehavior('ModelHistory.Historizable', [
            'customActions' => [
                'finished' => [
                    'status' => STATUS::COMPLETED,
                    'action' => 'finished',
                    'translation' => __('reviews.finished')
                ],
                'in_progress' => [
                    'status' => STATUS::IN_PROGRESS,
                    'action' => 'in_progress',
                    'translation' => __('reviews.in_progress')
                ],
                'decline' => [
                    'status' => STATUS::DECLINED,
                    'action' => 'decline',
                    'translation' => __('reviews.declined')
                ]
            ]
        ]);
        $article = $this->Articles->newEntity([
            'title' => 'foobar',
            'status' => 'new'
        ]);
        $this->Articles->save($article);
        $entry = $this->ModelHistory->find()->first();
        $this->assertEquals($entry->action, 'create');
        $data = [
            'status' => STATUS::IN_PROGRESS
        ];
        $article = $this->Articles->patchEntity($article, $data);
        $this->Articles->save($article);
        $entry = $this->ModelHistory->find()
            ->order(['revision' => 'DESC'])
            ->first();
        $this->assertEquals($entry->action, 'in_progress');
    }
}
