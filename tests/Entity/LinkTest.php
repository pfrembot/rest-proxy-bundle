<?php
/**
 * File LinkTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Tests\Entity;

use Pfrembot\RestProxyBundle\Entity\Link;

/**
 * Class LinkTest
 *
 * @coversDefaultClass Pfrembot\RestProxyBundle\Entity\Link
 *
 * @package Pfrembot\RestProxyBundle\Tests\Entity
 */
class LinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Link
     */
    private $link;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->link = new Link();
        $reflection = new \ReflectionObject($this->link);

        $hrefProperty = $reflection->getProperty('href');
        $hrefProperty->setAccessible(true);
        $hrefProperty->setValue($this->link, '/foo/path');
    }

    /**
     * @covers ::getHref
     */
    public function testGetHref()
    {
        $this->assertEquals('/foo/path', $this->link->getHref());
    }

    /**
     * @covers ::__toString
     */
    public function testToString()
    {
        $this->assertEquals('/foo/path', (string) $this->link);
    }
}
