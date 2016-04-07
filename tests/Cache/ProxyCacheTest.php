<?php
/**
 * File ProxyCacheTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Tests\Cache;

use Pfrembot\RestProxyBundle\Cache\ProxyCache;
use PhpParser\Builder\Namespace_;
use PhpParser\PrettyPrinter;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ProxyCacheTest
 *
 * @coversDefaultClass Pfrembot\RestProxyBundle\Cache\ProxyCache
 *
 * @package Pfrembot\RestProxyBundle\Tests\Cache
 */
class ProxyCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Filesystem|\Mockery\MockInterface
     */
    private $fs;

    /**
     * @var PrettyPrinter\Standard|\Mockery\MockInterface
     */
    private $printer;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->fs = \Mockery::mock(Filesystem::class);
        $this->printer = \Mockery::mock(PrettyPrinter\Standard::class);

        $this->cacheDir = __DIR__ . DIRECTORY_SEPARATOR . ProxyCache::CACHE_PATH;
    }

    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->fs->shouldReceive('exists')->once()->with($this->cacheDir)->andReturn(true);
        $this->fs->shouldNotReceive('mkdir');

        $cache = new ProxyCache($this->printer, $this->fs, __DIR__);

        $this->assertAttributeInstanceOf(PrettyPrinter\Standard::class, 'printer', $cache);
        $this->assertAttributeInstanceOf(Filesystem::class, 'fs', $cache);
        $this->assertAttributeEquals(__DIR__ . DIRECTORY_SEPARATOR . ProxyCache::CACHE_PATH, 'cacheDir', $cache);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructWithNoCacheDirectory()
    {
        $this->fs->shouldReceive('exists')->once()->with($this->cacheDir)->andReturn(false);
        $this->fs->shouldReceive('mkdir')->once()->with($this->cacheDir, 0775)->andReturnUndefined();

        $cache = new ProxyCache($this->printer, $this->fs, __DIR__);

        $this->assertAttributeInstanceOf(PrettyPrinter\Standard::class, 'printer', $cache);
        $this->assertAttributeInstanceOf(Filesystem::class, 'fs', $cache);
        $this->assertAttributeEquals(__DIR__ . DIRECTORY_SEPARATOR . ProxyCache::CACHE_PATH, 'cacheDir', $cache);
    }

    /**
     * @covers ::write
     */
    public function testWrite()
    {
        $this->fs->shouldReceive('exists')->andReturn(true);

        $cache = new ProxyCache($this->printer, $this->fs, __DIR__);
        $model = new Namespace_(self::class);

        $path = $this->cacheDir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, self::class) . '.php';

        $this->printer->shouldReceive('prettyPrintFile')->once()->with([$model->getNode()])->andReturn('<?php namespace ...');
        $this->fs->shouldReceive('dumpFile')->once()->with($path, '<?php namespace ...')->andReturnUndefined();

        $cache->write($model, self::class);
    }
}
