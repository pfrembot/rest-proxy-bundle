<?php
/**
 * File ConfigurationTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Tests\DependencyInjection;

use Pfrembot\RestProxyBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\ArrayNode;
use Symfony\Component\Config\Definition\PrototypedArrayNode;
use Symfony\Component\Config\Definition\ScalarNode;

/**
 * Class ConfigurationTest
 *
 * @coversDefaultClass Pfrembot\RestProxyBundle\DependencyInjection\Configuration
 *
 * @package Pfrembot\RestProxyBundle\Tests\DependencyInjection
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getConfigTreeBuilder
     */
    public function testGetConfigTreeBuilder()
    {
        $configuration = new Configuration();
        $treeBuilder = $configuration->getConfigTreeBuilder();

        /** @var ArrayNode $tree */
        $tree = $treeBuilder->buildTree();
        $this->assertInternalType('array', $tree->getChildren());
        $this->assertArrayHasKey('excluded_directories', $tree->getChildren());

        /** @var PrototypedArrayNode[] $children */
        $children = $tree->getChildren();
        $this->assertInstanceOf(PrototypedArrayNode::class, $children['excluded_directories']);
        $this->assertInstanceOf(ScalarNode::class, $children['excluded_directories']->getPrototype());
    }
}
