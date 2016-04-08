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
use PhpParser\Builder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

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
     * @var Finder|\Mockery\MockInterface
     */
    private $finder;

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
        $this->finder = \Mockery::mock(Finder::class);
        $this->builder = \Mockery::mock(ProxyBuilder::class);
        $this->cache = \Mockery::mock(ProxyCache::class);

        $this->cacheWarmer = new CacheWarmer($this->finder, $this->builder, $this->cache);
    }

    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertAttributeInstanceOf(Finder::class, 'finder', $this->cacheWarmer);
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

        $directory = str_replace('\\', '/', __NAMESPACE__);
        $classname = explode('\\', __CLASS__);

        $fileInfo = new SplFileInfo(__FILE__, $directory, end($classname));
        $iterator = new \ArrayIterator([$fileInfo]);

        $this->finder->shouldReceive('getIterator')->once()->withNoArgs()->andReturn($iterator);
        $this->builder->shouldReceive('build')->once()->with(\ReflectionClass::class)->andReturn($model);
        $this->cache->shouldReceive('write')->once()->with($model, self::class)->andReturnUndefined();

        $this->cacheWarmer->warmUp(__DIR__);
    }

    /**
     * @covers ::warmUp
     */
    public function testWarmUpWithNoAnnotation()
    {
        $directory = str_replace('\\', '/', __NAMESPACE__);
        $classname = explode('\\', __CLASS__);

        $fileInfo = new SplFileInfo(__FILE__, $directory, end($classname));
        $iterator = new \ArrayIterator([$fileInfo]);

        $this->finder->shouldReceive('getIterator')->once()->withNoArgs()->andReturn($iterator);
        $this->builder->shouldReceive('build')->once()->with(\ReflectionClass::class)->andReturn(false);
        $this->cache->shouldNotReceive('write');

        $this->cacheWarmer->warmUp(__DIR__);
    }
}
