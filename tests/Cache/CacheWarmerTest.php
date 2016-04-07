<?php
/**
 * File CacheWarmerTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */

namespace Pfrembot\RestProxyBundle\Tests\Cache;

use Pfrembot\RestProxyBundle\Builder\ProxyBuilder;
use Pfrembot\RestProxyBundle\Cache\CacheWarmer;
use Pfrembot\RestProxyBundle\Cache\ProxyCache;
use Pfrembot\RestProxyBundle\Finder\ProxyClassFinder;
use PhpParser\Builder;

/**
 * Class CacheWarmerTest
 *
 * @coversDefaultClass Pfrembot\RestProxyBundle\Cache\CacheWarmer
 *
 * @package Pfrembot\RestProxyBundle\Tests\Cache
 */
class CacheWarmerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProxyClassFinder|\Mockery\MockInterface
     */
    private $classFinder;

    /**
     * @var ProxyBuilder|\Mockery\MockInterface
     */
    private $builder;

    /**
     * @var ProxyCache|\Mockery\MockInterface
     */
    private $cache;

    /**
     * @var CacheWarmer
     */
    private $cacheWarmer;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->classFinder = \Mockery::mock(ProxyClassFinder::class);
        $this->builder = \Mockery::mock(ProxyBuilder::class);
        $this->cache = \Mockery::mock(ProxyCache::class);

        $this->cacheWarmer = new CacheWarmer($this->classFinder, $this->builder, $this->cache);
    }

    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertAttributeInstanceOf(ProxyClassFinder::class, 'classFinder', $this->cacheWarmer);
        $this->assertAttributeInstanceOf(ProxyBuilder::class, 'builder', $this->cacheWarmer);
        $this->assertAttributeInstanceOf(ProxyCache::class, 'cache', $this->cacheWarmer);
    }

    /**
     * @covers ::isOptional
     */
    public function testIsOptional()
    {
        $this->assertFalse($this->cacheWarmer->isOptional());
    }

    /**
     * @covers ::warmUp
     */
    public function testWarmUp()
    {
        $model = \Mockery::mock(Builder\Namespace_::class);

        $this->classFinder->shouldReceive('getAllClassNames')->once()->withNoArgs()->andReturn([self::class]);
        $this->builder->shouldReceive('build')->once()->with(\ReflectionClass::class)->andReturn($model);
        $this->cache->shouldReceive('write')->once()->with($model, self::class)->andReturnUndefined();
    }
}
