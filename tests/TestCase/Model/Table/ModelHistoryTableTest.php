<?php
namespace ModelHistory\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
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
        $config = TableRegistry::exists('ModelHistory') ? [] : ['className' => 'ModelHistory\Model\Table\ModelHistoryTable'];
        $this->ModelHistory = TableRegistry::get('ModelHistory', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ModelHistory);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {

    }
}
