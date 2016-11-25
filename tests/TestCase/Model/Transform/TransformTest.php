<?php
namespace ModelHistory\Test\TestCase\Model\Transform;

use Cake\TestSuite\TestCase;
use ModelHistory\Model\Transform\Transform;

/**
 * ModelHistory\Model\Transform\Transform Test Case
 */
class TransformTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @expectedException     InvalidArgumentException
     * @return void
     */
    public function testFaultyGet()
    {
        Transform::get('fo');
    }

    public function testStringTransformSave()
    {
        $trans = Transform::get('string');
        $this->assertEquals(true, $trans->save('foo', true, null));
    }
}
