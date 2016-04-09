<?php
/**
 * File LinkDictionaryTraitTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Tests\Mixin;

use Pfrembot\RestProxyBundle\Mixin\LinkDictionaryTrait;

/**
 * Class LinkDictionaryTraitTest
 *
 * @coversDefaultClass Pfrembot\RestProxyBundle\Mixin\LinkDictionaryTrait
 *
 * @package Pfrembot\RestProxyBundle\Tests\Mixin
 */
class LinkDictionaryTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LinkDictionaryTrait
     */
    private $trait;

    /**
     * @var \ReflectionObject
     */
    private $reflection;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->trait = $this->getObjectForTrait(LinkDictionaryTrait::class);
        $this->reflection = new \ReflectionObject($this->trait);

        $linksProperty = $this->reflection->getProperty('_links');
        $linksProperty->setAccessible(true);
        $linksProperty->setValue($this->trait, [
            'self' => '/self/path',
            'foo' => '/foo/path',
        ]);
    }

    /**
     * @covers ::getLink
     */
    public function testGetLink()
    {
        $method = $this->reflection->getMethod('getLink');
        $method->setAccessible(true);

        $this->assertEquals('/self/path', $method->invoke($this->trait, 'self'));
        $this->assertEquals('/foo/path', $method->invoke($this->trait, 'foo'));
    }

    /**
     * @covers ::getLink
     * @expectedException \LogicException
     */
    public function testGetLinkException()
    {
        $method = $this->reflection->getMethod('getLink');
        $method->setAccessible(true);

        $method->invoke($this->trait, 'noop');
    }
}
