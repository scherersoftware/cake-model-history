<?php
namespace ModelHistory\Test\TestCase\Model\Table;

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
        'ModelHistory' => 'plugin.model_history.model_history'
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
     * Test initialize method
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

        $this->assertEquals($entry->data['id'], $article->id);
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
}
