<?php
/**
 * File Link.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Entity;

use JMS\Serializer\Annotation as JMS;

/**
 * Class Link
 *
 * @package Pfrembot\RestProxyBundle\Entity
 */
class Link
{
    /**
     * @JMS\Type("string")
     *
     * @var string
     */
    private $href;

    /**
     * Return link href
     *
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->href;
    }
}
