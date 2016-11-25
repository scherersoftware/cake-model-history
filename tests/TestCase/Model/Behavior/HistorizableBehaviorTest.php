<?php
namespace ModelHistory\Test\TestCase\Model\Behavior;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use ModelHistory\Model\Behavior\HistorizableBehavior;

/**
 * ModelHistory\Model\Behavior\HistorizableBehavior Test Case
 */
class HistorizableBehaviorTest extends TestCase
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
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        $this->Articles = TableRegistry::get('ArticlesTable', ['className' => 'ModelHistoryTestApp\Model\Table\ArticlesTable']);
        $this->ModelHistory = TableRegistry::get('ModelHistory', ['className' => 'ModelHistory\Model\Table\ModelHistoryTable']);

        parent::setUp();
        TableRegistry::clear();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Articles);
        unset($this->ModelHistory);
        parent::tearDown();
    }
    /**
     * Test initial setup
     *
     * @return void
     */
    public function testGetRelationLink()
    {
        $userId = '481fc6d0-b920-43e0-a40d-6d1740cf8562';
        $articlesId = '99dbcad7-21d5-4dd1-b193-e00543c0224c';


        $article = $this->Articles->newEntity([
            'articles_id' => $articlesId,
        ]);
        $this->Articles->save($article);
    }
}
