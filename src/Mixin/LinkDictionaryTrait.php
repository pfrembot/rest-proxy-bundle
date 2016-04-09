<?php
/**
 * File LinkDictionaryTrait.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Mixin;

use JMS\Serializer\Annotation as JMS;
use Pfrembot\RestProxyBundle\Entity\Link;

/**
 * Class LinkDictionaryTrait
 *
 * @package Pfrembot\RestProxyBundleMixin
 */
trait LinkDictionaryTrait
{
    /**
     * @JMS\Type("array<Pfrembot\RestProxyBundle\Entity\Link>")
     *
     * @var array|Link[]
     */
    private $_links = [];

    /**
     * Return class link url
     *
     * @param string $key
     * @return string
     */
    protected function getLink($key)
    {
        if (!array_key_exists($key, $this->_links)) {
            throw new \LogicException(
                sprintf('Link "%s" not found on class. Available links [%s]', $key, join(',', array_keys($this->_links)))
            );
        }

        return $this->_links[$key];
    }
}
