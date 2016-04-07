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
        $builder->setParameter('kernel.cache_dir', __DIR__);

        // assert services exist
        $this->assertTrue($builder->has('rest_proxy.php_parser.lexer'));
        $this->assertTrue($builder->has('rest_proxy.php_parser.traverser'));
        $this->assertTrue($builder->has('rest_proxy.php_parser.printer'));
        $this->assertTrue($builder->has('rest_proxy.php_parser.parser'));
        $this->assertTrue($builder->has('rest_proxy.symfony.filesystem'));
        $this->assertTrue($builder->has('rest_proxy.symfony.finder'));
        $this->assertTrue($builder->has('rest_proxy.parser.class_parser'));
        $this->assertTrue($builder->has('rest_proxy.finder.class_finder'));
        $this->assertTrue($builder->has('rest_proxy.builder.proxy_builder'));
        $this->assertTrue($builder->has('rest_proxy.proxy_cache'));
        $this->assertTrue($builder->has('rest_proxy.cache_warmer'));
        $this->assertTrue($builder->has('rest_proxy.serializer.subscriber'));

        // bundle service definitions
        $PhpParserLexerDefinition = $builder->getDefinition('rest_proxy.php_parser.lexer');
        $PhpParserTraverserDefinition = $builder->getDefinition('rest_proxy.php_parser.traverser');
        $PhpParserPrinterDefinition = $builder->getDefinition('rest_proxy.php_parser.printer');
        $PhpParserParserDefinition = $builder->getDefinition('rest_proxy.php_parser.parser');
        $SymfonyFilesystemDefinition = $builder->getDefinition('rest_proxy.symfony.filesystem');
        $SymfonyFinderDefinition = $builder->getDefinition('rest_proxy.symfony.finder');
        $RestProxyClassParserDefinition = $builder->getDefinition('rest_proxy.parser.class_parser');
        $RestProxyClassFinderDefinition = $builder->getDefinition('rest_proxy.finder.class_finder');
        $RestProxyProxyBuilderDefinition = $builder->getDefinition('rest_proxy.builder.proxy_builder');
        $RestProxyProxyCacheDefinition = $builder->getDefinition('rest_proxy.proxy_cache');
        $RestProxyCacheWarmerDefinition = $builder->getDefinition('rest_proxy.cache_warmer');
        $RestProxySerializerSubscriberDefinition = $builder->getDefinition('rest_proxy.serializer.subscriber');

        // assert correct service classes
        $this->assertEquals(\PhpParser\Lexer::class, $PhpParserLexerDefinition->getClass());
        $this->assertEquals(\PhpParser\NodeTraverser::class, $PhpParserTraverserDefinition->getClass());
        $this->assertEquals(\PhpParser\PrettyPrinter\Standard::class, $PhpParserPrinterDefinition->getClass());
        $this->assertEquals(\PhpParser\Parser::class, $PhpParserParserDefinition->getClass());
        $this->assertEquals(\Symfony\Component\Filesystem\Filesystem::class, $SymfonyFilesystemDefinition->getClass());
        $this->assertEquals(\Symfony\Component\Finder\Finder::class, $SymfonyFinderDefinition->getClass());
        $this->assertEquals(\Pfrembot\RestProxyBundle\Parser\ClassFileParser::class, $RestProxyClassParserDefinition->getClass());
        $this->assertEquals(\Pfrembot\RestProxyBundle\Finder\ProxyClassFinder::class, $RestProxyClassFinderDefinition->getClass());
        $this->assertEquals(\Pfrembot\RestProxyBundle\Builder\ProxyBuilder::class, $RestProxyProxyBuilderDefinition->getClass());
        $this->assertEquals(\Pfrembot\RestProxyBundle\Cache\ProxyCache::class, $RestProxyProxyCacheDefinition->getClass());
        $this->assertEquals(\Pfrembot\RestProxyBundle\Cache\CacheWarmer::class, $RestProxyCacheWarmerDefinition->getClass());
        $this->assertEquals(\Pfrembot\RestProxyBundle\Subscriber\SerializerSubscriber::class, $RestProxySerializerSubscriberDefinition->getClass());

        // all services are private
        $this->assertFalse($PhpParserLexerDefinition->isPublic());
        $this->assertFalse($PhpParserTraverserDefinition->isPublic());
        $this->assertFalse($PhpParserPrinterDefinition->isPublic());
        $this->assertFalse($PhpParserParserDefinition->isPublic());
        $this->assertFalse($SymfonyFilesystemDefinition->isPublic());
        $this->assertFalse($SymfonyFinderDefinition->isPublic());
        $this->assertFalse($RestProxyClassParserDefinition->isPublic());
        $this->assertFalse($RestProxyClassFinderDefinition->isPublic());
        $this->assertFalse($RestProxyProxyBuilderDefinition->isPublic());
        $this->assertFalse($RestProxyProxyCacheDefinition->isPublic());
        $this->assertFalse($RestProxyCacheWarmerDefinition->isPublic());
        $this->assertFalse($RestProxySerializerSubscriberDefinition->isPublic());
    }
}
