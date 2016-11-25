<?php
namespace ModelHistory\Test\TestCase\Model\Entity;

use Cake\Network\Request;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use ModelHistoryTestApp\Model\Entity\Article;
use ModelHistory\Model\Entity\ModelHistory;

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
     * Test wrong type
     *
     * @expectedException     InvalidArgumentException
     * @return void
     */
    public function testHistoryContextWrongType()
    {
        $dataObject = new \stdClass();
        $article = $this->Articles->newEntity();
        $article->setHistoryContext('foo');
    }

    /**
     * Test setting of faulty shell context
     *
     * @expectedException     InvalidArgumentException
     * @return void
     */
    public function testSettingFaultyShellContext()
    {
        $article = $this->Articles->newEntity();
        $article->setHistoryContext(ModelHistory::CONTEXT_TYPE_SHELL, null);
        $this->Articles->save($article);
    }

    /**
     * Test setting of shell context
     *
     * @return void
     */
    public function testSettingShellContext()
    {
        $dataObject = new \Cake\Console\Shell();

        $dataObject->OptionParser = 'OptionParser';
        $dataObject->interactive = 'interactive';
        $dataObject->params = 'params';
        $dataObject->command = 'command';
        $dataObject->args = 'args';
        $dataObject->name = 'name';
        $dataObject->plugin = 'plugin';
        $dataObject->tasks = 'tasks';
        $dataObject->taskNames = 'taskNames';

        $slug = 'a_slug';

        $article = $this->Articles->newEntity();
        $article->setHistoryContext(ModelHistory::CONTEXT_TYPE_SHELL, $dataObject, $slug);
        $this->Articles->save($article);

        $modelHistory = $this->ModelHistory->find()->toArray();
        $this->assertEquals(1, count($modelHistory));

        $this->assertNotEmpty($modelHistory[0]->context);
        $this->assertEquals(ModelHistory::CONTEXT_TYPE_SHELL, $modelHistory[0]->context_type);
        $this->assertEquals($slug, $modelHistory[0]->context_slug);
    }

    /**
     * Test setting of faulty controller context
     *
     * @expectedException     InvalidArgumentException
     * @return void
     */
    public function testSettingFaultyControllerContext()
    {
        $article = $this->Articles->newEntity();
        $article->setHistoryContext(ModelHistory::CONTEXT_TYPE_CONTROLLER, null);
        $this->Articles->save($article);
    }

    /**
     * Test setting of controller context
     *
     * @return void
     */
    public function testSettingControllerContext()
    {
        $slug = 'a_slug';
        $request = new Request();

        $article = $this->Articles->newEntity();
        $article->setHistoryContext(ModelHistory::CONTEXT_TYPE_CONTROLLER, $request, $slug);
        $this->Articles->save($article);

        $modelHistory = $this->ModelHistory->find()->toArray();
        $this->assertEquals(1, count($modelHistory));

        $this->assertNotEmpty($modelHistory[0]->context);
        $this->assertEquals(ModelHistory::CONTEXT_TYPE_CONTROLLER, $modelHistory[0]->context_type);
        $this->assertEquals($slug, $modelHistory[0]->context_slug);

        $this->Articles->patchEntity($article, ['title' => 'asdf']);
        $article->setHistoryContext(ModelHistory::CONTEXT_TYPE_CONTROLLER, $request);
        $this->Articles->save($article);

        $modelHistory = $this->ModelHistory
            ->find()
            ->order(['revision' => 'DESC'])
            ->where(['foreign_key' => $article->id])
            ->first();

        $this->assertInstanceOf('ModelHistory\Model\Entity\ModelHistory', $modelHistory);

        $this->assertNotEmpty($modelHistory->context);
        $this->assertEquals(ModelHistory::CONTEXT_TYPE_CONTROLLER, $modelHistory->context_type);
        $this->assertEquals('//', $modelHistory->context_slug);
    }

    /**
     * Test setting of faulty slug context
     *
     * @expectedException     InvalidArgumentException
     * @return void
     */
    public function testSettingFaultySlugContext()
    {
        $article = $this->Articles->newEntity();
        $article->setHistoryContext(ModelHistory::CONTEXT_TYPE_SLUG, null, null);
        $this->Articles->save($article);
    }

    /**
     * Test setting of slug context
     *
     * @return void
     */
    public function testSettingSlugContext()
    {
        $slug = 'asdf';
        $article = $this->Articles->newEntity();
        $article->setHistoryContext(ModelHistory::CONTEXT_TYPE_SLUG, null, $slug);
        $this->Articles->save($article);

        $modelHistory = $this->ModelHistory->find()->toArray();
        $this->assertEquals(1, count($modelHistory));

        $this->assertNotEmpty($modelHistory[0]->context);
        $this->assertEquals(['type' => 'slug'], $modelHistory[0]->context);
        $this->assertEquals(ModelHistory::CONTEXT_TYPE_SLUG, $modelHistory[0]->context_type);
        $this->assertEquals($slug, $modelHistory[0]->context_slug);
    }
}
