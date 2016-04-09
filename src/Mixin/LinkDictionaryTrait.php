<?php
/**
 * File LinkDictionaryTrait.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Mixin;

use JMS\Serializer\Annotation as JMS;

/**
 * Class LinkDictionaryTrait
 *
 * @package Pfrembot\RestProxyBundleMixin
 */
trait LinkDictionaryTrait
{
    /**
     * @JMS\Type("array")
     *
     * @var array|string[]
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
                sprintf('Link "%s" not found on class. Available links [%s]', $key, join(',', $this->_links))
            );
        }

        return $this->_links[$key];
    }
}
