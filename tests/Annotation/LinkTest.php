<?php
/**
 * File LinkTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Tests\Annotation;

use Pfrembot\RestProxyBundle\Annotation as RestProxy;

/**
 * Class LinkTest
 *
 * @coversDefaultClass Pfrembot\RestProxyBundle\Annotation\Link
 *
 * @package Pfrembot\RestProxyBundle\Tests\Annotation
 */
class LinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $annotation = new RestProxy\Link([
            'value' => 'testKey',
            'service' => 'test.service',
            'method' => 'testMethod',
            'property' => 'test',
        ]);

        $this->assertInstanceOf(RestProxy\Link::class, $annotation);
        $this->assertInstanceOf(RestProxy\Call::class, $annotation);
        $this->assertAttributeEquals('testKey', 'value', $annotation);
        $this->assertAttributeEquals('test.service', 'service', $annotation);
        $this->assertAttributeEquals('testMethod', 'method', $annotation);
        $this->assertAttributeEquals('test', 'property', $annotation);
    }

    /**
     * @covers ::getLink
     */
    public function testGetLink()
    {
        $annotation = new RestProxy\Link([
            'value' => 'testKey',
        ]);

        $this->assertEquals('testKey', $annotation->getLink());
    }

    /**
     * @covers ::getLink
     * @expectedException \LogicException
     */
    public function testGetLinkException()
    {
        $annotation = new RestProxy\Link([]);

        $annotation->getLink();
    }

    /**
     * @covers ::getProperty
     */
    public function testGetProperty()
    {
        $annotation = new RestProxy\Link([
            'property' => 'testKey',
        ]);

        $this->assertEquals('testKey', $annotation->getProperty());
    }

    /**
     * @covers ::getProperty
     */
    public function testGetPropertyFromValue()
    {
        $annotation = new RestProxy\Link([
            'value' => 'testKey',
        ]);

        $this->assertEquals('testKey', $annotation->getProperty());
    }

    /**
     * @covers ::getArguments
     */
    public function testGetArguments()
    {
        $annotation = new RestProxy\Link([
            'value' => 'testKey',
        ]);

        $this->assertEquals('[$this->getLink("testKey")]', $annotation->getArguments());
    }
}
