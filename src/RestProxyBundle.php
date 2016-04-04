<?php
/**
 * File Pfrembot\RestProxyBundle.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle;

use Pfrembot\RestProxyBundle\Cache\ProxyCache;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class Pfrembot\RestProxyBundle
 *
 * @package Pfrembot\RestProxyBundle
 */
class RestProxyBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $cacheDir = $this->container->getParameter('kernel.cache_dir');
        $rootDir = $this->container->getParameter('kernel.root_dir');

        $loader = require $rootDir . '/../vendor/autoload.php';
        $loader->addPsr4(ProxyCache::NAMESPACE_PREFIX . '\\', $cacheDir . DIRECTORY_SEPARATOR . ProxyCache::CACHE_PATH);
    }
}
