<?php
namespace ModelHistory\Test\TestCase\Model\Entity;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use ModelHistoryTestApp\Model\Entity\Article;

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
    }
}
