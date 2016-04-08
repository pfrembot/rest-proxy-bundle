<?php
/**
 * File RestProxyExtension.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Tests\DependencyInjection;

use Pfrembot\RestProxyBundle\DependencyInjection\RestProxyExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class RestProxyExtensionTest
 *
 * @coversDefaultClass Pfrembot\RestProxyBundle\DependencyInjection\RestProxyExtension
 *
 * @package Pfrembot\RestProxyBundle\Tests\DependencyInjection
 */
class RestProxyExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::load
     */
    public function testLoad()
    {
        $extension = new RestProxyExtension();
        $builder = new ContainerBuilder();

        $extension->load([], $builder);

        // set expected symfony parameters
        $builder->setParameter('kernel.root_dir', dirname(__DIR__));
        $builder->setParameter('kernel.cache_dir', '/tmp');

        // assert parameters
        $this->assertTrue($builder->hasParameter('pfrembot_rest_proxy.excluded_directories'));
        $this->assertEquals(['Test','Tests'], $builder->getParameter('pfrembot_rest_proxy.excluded_directories'));

        // assert services exist
        $this->assertTrue($builder->has('pfrembot_rest_proxy.php_parser.printer'));
        $this->assertTrue($builder->has('pfrembot_rest_proxy.symfony.filesystem'));
        $this->assertTrue($builder->has('pfrembot_rest_proxy.symfony.finder'));
        $this->assertTrue($builder->has('pfrembot_rest_proxy.builder.proxy_builder'));
        $this->assertTrue($builder->has('pfrembot_rest_proxy.proxy_cache'));
        $this->assertTrue($builder->has('pfrembot_rest_proxy.cache_warmer'));
        $this->assertTrue($builder->has('pfrembot_rest_proxy.serializer.subscriber'));

        // bundle service definitions
        $PhpParserPrinterDefinition = $builder->getDefinition('pfrembot_rest_proxy.php_parser.printer');
        $SymfonyFilesystemDefinition = $builder->getDefinition('pfrembot_rest_proxy.symfony.filesystem');
        $SymfonyFinderDefinition = $builder->getDefinition('pfrembot_rest_proxy.symfony.finder');
        $RestProxyProxyBuilderDefinition = $builder->getDefinition('pfrembot_rest_proxy.builder.proxy_builder');
        $RestProxyProxyCacheDefinition = $builder->getDefinition('pfrembot_rest_proxy.proxy_cache');
        $RestProxyCacheWarmerDefinition = $builder->getDefinition('pfrembot_rest_proxy.cache_warmer');
        $RestProxySerializerSubscriberDefinition = $builder->getDefinition('pfrembot_rest_proxy.serializer.subscriber');

        // assert correct service classes
        $this->assertEquals(\PhpParser\PrettyPrinter\Standard::class, $PhpParserPrinterDefinition->getClass());
        $this->assertEquals(\Symfony\Component\Filesystem\Filesystem::class, $SymfonyFilesystemDefinition->getClass());
        $this->assertEquals(\Symfony\Component\Finder\Finder::class, $SymfonyFinderDefinition->getClass());
        $this->assertEquals(\Pfrembot\RestProxyBundle\Builder\ProxyBuilder::class, $RestProxyProxyBuilderDefinition->getClass());
        $this->assertEquals(\Pfrembot\RestProxyBundle\Cache\ProxyCache::class, $RestProxyProxyCacheDefinition->getClass());
        $this->assertEquals(\Pfrembot\RestProxyBundle\Cache\CacheWarmer::class, $RestProxyCacheWarmerDefinition->getClass());
        $this->assertEquals(\Pfrembot\RestProxyBundle\Subscriber\SerializerSubscriber::class, $RestProxySerializerSubscriberDefinition->getClass());

        // all services are private
        $this->assertFalse($PhpParserPrinterDefinition->isPublic());
        $this->assertFalse($SymfonyFilesystemDefinition->isPublic());
        $this->assertFalse($SymfonyFinderDefinition->isPublic());
        $this->assertFalse($RestProxyProxyBuilderDefinition->isPublic());
        $this->assertFalse($RestProxyProxyCacheDefinition->isPublic());
        $this->assertFalse($RestProxyCacheWarmerDefinition->isPublic());
        $this->assertTrue($RestProxySerializerSubscriberDefinition->isPublic());
    }
}
