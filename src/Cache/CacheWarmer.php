<?php
/**
 * File CacheWarmer.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Cache;

use Pfrembot\RestProxyBundle\Builder\ProxyBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * Class CacheWarmer
 *
 * @package Pfrembot\RestProxyBundle\Cache
 */
class CacheWarmer implements CacheWarmerInterface
{
    /**
     * @var Finder
     */
    private $finder;

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
     * @param Finder $finder
     * @param ProxyBuilder $builder
     * @param ProxyCache $cache
     */
    public function __construct(Finder $finder, ProxyBuilder $builder, ProxyCache $cache)
    {
        $this->finder = $finder;
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
        /** @var SplFileInfo $file */
        foreach ($this->finder as $file) {
            $filename = $file->getRelativePath() . '/' . $file->getBasename('.php');
            $classname = str_replace('/', '\\', $filename);

            $reflection = new \ReflectionClass($classname);

            $model = $this->builder->build($reflection);

            if (!$model) {
                continue;
            }

            $this->cache->write($model, $reflection->getName());
        }
    }
}
