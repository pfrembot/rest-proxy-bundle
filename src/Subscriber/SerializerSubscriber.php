<?php
/**
 * File SerializerSubscriber.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Subscriber;

use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use Pfrembot\RestProxyBundle\Annotation\Entity;
use Pfrembot\RestProxyBundle\Cache\ProxyCache;
use Pfrembot\RestProxyBundle\Proxy\ProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SerializerSubscriber
 *
 * @package Pfrembot\RestProxyBundle\Subscriber
 */
class SerializerSubscriber implements EventSubscriberInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param Reader $reader
     * @param ContainerInterface $container
     */
    public function __construct(Reader $reader, ContainerInterface $container)
    {
        $this->reader = $reader;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ['event' => Events::PRE_DESERIALIZE, 'method' => 'onPreDeserialize'],
            ['event' => Events::POST_DESERIALIZE, 'method' => 'onPostDeserialize'],
        ];
    }

    /**
     * Replace proxied entities during JMS deserialization
     *
     * @param PreDeserializeEvent $event
     */
    public function onPreDeserialize(PreDeserializeEvent $event)
    {
        $name = $event->getType()['name'];
        $class = new \ReflectionClass($name);

        if ($annotation = $this->reader->getClassAnnotation($class, Entity::class)) {
            $event->setType(sprintf('%s\\%s', ProxyCache::NAMESPACE_PREFIX, $name));
        }
    }

    /**
     * Initialize proxy container service after deserialization is completed
     *
     * @param ObjectEvent $event
     */
    public function onPostDeserialize(ObjectEvent $event)
    {
        $object = $event->getObject();

        if ($object instanceof ProxyInterface) {
            $object->__initialize__($this->container);
        }
    }
}
