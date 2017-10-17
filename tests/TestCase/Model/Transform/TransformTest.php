<?php
declare(strict_types = 1);
namespace ModelHistory\Test\TestCase\Model\Transform;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use ModelHistory\Model\Transform\Transform;

/**
 * ModelHistory\Model\Transform\Transform Test Case
 */
class TransformTest extends TestCase
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
    public function setUp(): void
    {
        $this->Articles = TableRegistry::get('ArticlesTable', ['className' => 'ModelHistoryTestApp\Model\Table\ArticlesTable']);

        parent::setUp();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @expectedException     InvalidArgumentException
     * @return void
     */
    public function testFaultyGet(): void
    {
        Transform::get('fo');
    }

    public function testStringTransformSave(): void
    {
        $aritcle = $this->Articles->get('7997df22-ed8e-4703-b971-d9514179904b');

        $trans = Transform::get('string');
        $this->assertEquals('check', $trans->save('content', [], $aritcle));
    }

    public function testRelationTransformSave(): void
    {
        $aritcle = $this->Articles->get('7997df22-ed8e-4703-b971-d9514179904b');

        $trans = Transform::get('relation');
        $this->assertEquals('check', $trans->save('content', [], $aritcle));
    }

    public function testRelationTransformDisplay(): void
    {
        $trans = Transform::get('relation');
        $this->assertEquals('7997df22-ed8e-4703-b971-d9514179904b', $trans->display('article_id', '7997df22-ed8e-4703-b971-d9514179904b', null));
    }

    public function testAssociationTransformSave(): void
    {
        $aritcle = $this->Articles->get('7997df22-ed8e-4703-b971-d9514179904b');

        $trans = Transform::get('association');
        $this->assertEquals('check', $trans->save('content', ['associationKey' => 'content'], $aritcle));
    }

    public function testAssociationTransformDisplay(): void
    {
        $trans = Transform::get('association');
        $this->assertEquals('7997df22-ed8e-4703-b971-d9514179904b', $trans->display('article_id', '7997df22-ed8e-4703-b971-d9514179904b', null));
    }
}
