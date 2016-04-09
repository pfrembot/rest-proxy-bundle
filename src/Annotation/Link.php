<?php
/**
 * File Link.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Link
 *
 * Specify an embedded class like to follow for retrieving embedded
 * entity data. Used to lazy load entity data.
 *
 * @Annotation
 * @Target("METHOD")
 *
 * @package Pfrembot\RestProxyBundle\Annotation
 */
class Link extends Call
{
    /**
     * Return link key
     *
     * @return string
     */
    public function getLink()
    {
        if (!$this->value) {
            throw new \LogicException('Link annotation value is required');
        }

        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty()
    {
        if (!$this->property) {
            $this->property = $this->value;
        }

        return parent::getProperty();
    }

    /**
     * Return link url as proxy method argument
     *
     * {@inheritdoc}
     *
     * @return array
     */
    public function getArguments()
    {
        return sprintf('[$this->getLink("%s")]', $this->getLink());
    }
}
