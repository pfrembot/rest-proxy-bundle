<?php
/**
 * File CallTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Tests\Annotation;

use Pfrembot\RestProxyBundle\Annotation as RestProxy;

/**
 * Class CallTest
 *
 * @coversDefaultClass Pfrembot\RestProxyBundle\Annotation\Call
 *
 * @package Pfrembot\RestProxyBundle\Tests\Annotation
 */
class CallTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $annotation = new RestProxy\Call([
            'service' => 'test.service',
            'method' => 'testMethod',
            'property' => 'test',
            'arguments' => '[$this->id]',
        ]);

        $this->assertInstanceOf(RestProxy\Call::class, $annotation);
        $this->assertAttributeEquals('test.service', 'service', $annotation);
        $this->assertAttributeEquals('testMethod', 'method', $annotation);
        $this->assertAttributeEquals('test', 'property', $annotation);
        $this->assertAttributeEquals('[$this->id]', 'arguments', $annotation);
    }

    /**
     * @covers ::getService
     */
    public function testGetService()
    {
        $annotation = new RestProxy\Call([
            'service' => 'test.service',
        ]);

        $this->assertEquals('test.service', $annotation->getService());
    }

    /**
     * @covers ::getService
     * @expectedException \LogicException
     */
    public function testGetServiceException()
    {
        $annotation = new RestProxy\Call([]);
        $annotation->getService();
    }

    /**
     * @covers ::getMethod
     */
    public function testGetMethod()
    {
        $annotation = new RestProxy\Call([
            'method' => 'testMethod',
        ]);

        $this->assertEquals('testMethod', $annotation->getMethod());
    }

    /**
     * @covers ::getMethod
     * @expectedException \LogicException
     */
    public function testGetMethodException()
    {
        $annotation = new RestProxy\Call([]);
        $annotation->getMethod();
    }

    /**
     * @covers ::getProperty
     */
    public function testGetProperty()
    {
        $annotation = new RestProxy\Call([
            'property' => 'test',
        ]);

        $this->assertEquals('test', $annotation->getProperty());
    }

    /**
     * @covers ::getProperty
     * @expectedException \LogicException
     */
    public function testGetPropertyException()
    {
        $annotation = new RestProxy\Call([]);
        $annotation->getProperty();
    }

    /**
     * @covers ::getArguments
     */
    public function testGetArguments()
    {
        $annotation = new RestProxy\Call([
            'arguments' => '[$this->id]',
        ]);

        $this->assertEquals('[$this->id]', $annotation->getArguments());
    }

    /**
     * @covers ::getArguments
     * @expectedException \LogicException
     */
    public function testGetArgumentsException()
    {
        $annotation = new RestProxy\Call([
            'arguments' => ['$this->id'],
        ]);

        $annotation->getArguments();
    }
}
