<?php
/**
 * File CacheWarmer.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Cache;

use Pfrembot\RestProxyBundle\Builder\ProxyBuilder;
use Pfrembot\RestProxyBundle\Finder\ProxyClassFinder;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * Class CacheWarmer
 *
 * @package Pfrembot\RestProxyBundle\Cache
 */
class CacheWarmer implements CacheWarmerInterface
{
    /**
     * @var ProxyClassFinder
     */
    private $classFinder;

    /**
     * @var ProxyBuilder
     */
    private $builder;

    /**
     * @var ProxyCache
     */
    private $cache;

    /**
     * CacheWarmer constructor
     *
     * @param ProxyClassFinder $classFinder
     * @param ProxyBuilder $builder
     * @param ProxyCache $cache
     */
    public function __construct(ProxyClassFinder $classFinder, ProxyBuilder $builder, ProxyCache $cache)
    {
        $this->classFinder = $classFinder;
        $this->builder = $builder;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return false;
    }

    /**
     * Warm proxy cache
     *
     * @param string $cacheDir
     */
    public function warmUp($cacheDir)
    {
        $classes = $this->classFinder->getAllClassNames();

        foreach ($classes as $class) {
            $reflection = new \ReflectionClass($class);
            $model = $this->builder->build($reflection);

            $this->cache->write($model, $reflection->getName());
        }
    }
}
