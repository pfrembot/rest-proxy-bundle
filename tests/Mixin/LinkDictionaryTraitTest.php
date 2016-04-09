<?php
/**
 * File LinkDictionaryTraitTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Tests\Mixin;

use Pfrembot\RestProxyBundle\Entity\Link;
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
     * @var Link[]
     */
    private $links;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->trait = $this->getObjectForTrait(LinkDictionaryTrait::class);
        $this->reflection = new \ReflectionObject($this->trait);
        $this->links = [
            'self' => new Link(),
            'foo' => new Link(),
        ];

        $linksProperty = $this->reflection->getProperty('_links');
        $linksProperty->setAccessible(true);
        $linksProperty->setValue($this->trait, $this->links);
    }

    /**
     * @covers ::getLink
     */
    public function testGetLink()
    {
        $method = $this->reflection->getMethod('getLink');
        $method->setAccessible(true);

        $this->assertSame($this->links['self'], $method->invoke($this->trait, 'self'));
        $this->assertSame($this->links['foo'], $method->invoke($this->trait, 'foo'));
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
