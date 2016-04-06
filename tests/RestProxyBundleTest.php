<?php
/**
 * File RestProxyBundleTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Tests;

use Pfrembot\RestProxyBundle\RestProxyBundle;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RestProxyBundleTest
 *
 * @package Pfrembot\RestProxyBundle\Tests
 */
class RestProxyBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerInterface|\Mockery\MockInterface
     */
    private $container;

    /**
     * @var RestProxyBundle
     */
    private $bundle;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->container = \Mockery::mock(ContainerInterface::class);
        $this->bundle = new RestProxyBundle();

        $this->bundle->setContainer($this->container);
    }

    public function testBoot()
    {
        $this->container->shouldReceive('getParameter')->once()->with('kernel.cache_dir')->andReturn('/tmp');
        $this->container->shouldReceive('getParameter')->once()->with('kernel.root_dir')->andReturn(__DIR__.'/_mock/vendor');

        $this->bundle->boot();
    }
}
