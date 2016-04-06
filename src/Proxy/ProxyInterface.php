<?php
/**
 * File ProxyInterface.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Proxy;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Interface ProxyInterface
 *
 * @package Pfrembot\RestProxyBundle\Proxy
 */
interface ProxyInterface
{
    /**
     * Initializes dependency injection container
     * on proxied class
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function __initialize__(ContainerInterface $container);
}
