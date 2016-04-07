<?php
/**
 * File SerializerSubscriberTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Tests\Subscriber;

use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use Pfrembot\RestProxyBundle\Annotation as RestProxy;
use Pfrembot\RestProxyBundle\Cache\ProxyCache;
use Pfrembot\RestProxyBundle\Proxy\ProxyInterface;
use Pfrembot\RestProxyBundle\Subscriber\SerializerSubscriber;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SerializerSubscriberTest
 *
 * @coversDefaultClass Pfrembot\RestProxyBundle\Subscriber\SerializerSubscriber
 *
 * @package Pfrembot\RestProxyBundle\Tests\Subscriber
 */
class SerializerSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Reader|\Mockery\MockInterface
     */
    private $reader;

    /**
     * @var ContainerInterface|\Mockery\MockInterface
     */
    private $container;

    /**
     * @var SerializerSubscriber
     */
    private $subscriber;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->reader = \Mockery::mock(Reader::class);
        $this->container = \Mockery::mock(ContainerInterface::class);

        $this->subscriber = new SerializerSubscriber($this->reader, $this->container);
    }

    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertAttributeInstanceOf(Reader::class, 'reader', $this->subscriber);
        $this->assertAttributeInstanceOf(ContainerInterface::class, 'container', $this->subscriber);
    }

    /**
     * @covers ::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $events = $this->subscriber->getSubscribedEvents();

        $this->assertInternalType('array', $events);
        $this->assertContainsOnly('array', $events);

        foreach ($events as $event) {
            $this->assertArrayHasKey('event', $event);
            $this->assertArrayHasKey('method', $event);
        }
    }

    /**
     * @covers ::onPreDeserialize
     */
    public function testOnPreDeserializeWithProxyEntity()
    {
        $event = \Mockery::mock(PreDeserializeEvent::class);

        $this->reader->shouldReceive('getClassAnnotation')->once()->with(\ReflectionClass::class, RestProxy\Entity::class)->andReturn(true);

        $event->shouldReceive('getType')->once()->withNoArgs()->andReturn(['name' => __CLASS__]);
        $event->shouldReceive('setType')->once()->with(sprintf('%s\\%s', ProxyCache::NAMESPACE_PREFIX, __CLASS__))->andReturnUndefined();

        $this->subscriber->onPreDeserialize($event);
    }

    /**
     * @covers ::onPreDeserialize
     */
    public function testOnPreDeserializeWithNonProxyEntity()
    {
        $event = \Mockery::mock(PreDeserializeEvent::class);

        $this->reader->shouldReceive('getClassAnnotation')->once()->with(\ReflectionClass::class, RestProxy\Entity::class)->andReturn(false);

        $event->shouldReceive('getType')->once()->withNoArgs()->andReturn(['name' => __CLASS__]);
        $event->shouldNotReceive('setType');

        $this->subscriber->onPreDeserialize($event);
    }

    /**
     * @covers ::onPostDeserialize
     */
    public function testOnPostDeserializeWithProxyEntity()
    {
        $event = \Mockery::mock(ObjectEvent::class);
        $object = \Mockery::mock(ProxyInterface::class);

        $event->shouldReceive('getObject')->once()->withNoArgs()->andReturn($object);
        $object->shouldReceive('__initialize__')->once()->with($this->container)->andReturnUndefined();

        $this->subscriber->onPostDeserialize($event);
    }

    /**
     * @covers ::onPostDeserialize
     */
    public function testOnPostDeserializeWithNonProxyEntity()
    {
        $event = \Mockery::mock(ObjectEvent::class);
        $object = \Mockery::mock();

        $event->shouldReceive('getObject')->once()->withNoArgs()->andReturn($object);
        $object->shouldNotReceive('__initialize__');

        $this->subscriber->onPostDeserialize($event);
    }
}
