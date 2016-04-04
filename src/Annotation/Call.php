<?php
/**
 * File Call.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Call
 *
 * Designate method to be proxied to an API service method. Used to LAZY load
 * linked entities form an API service
 *
 * @Annotation
 * @Target("METHOD")
 */
class Call extends Annotation
{
    /**
     * @var string
     */
    public $property;

    /**
     * @var string
     */
    public $service;

    /**
     * @var string
     */
    public $method;

    /**
     * @var string
     */
    public $arguments = "array()";

    /**
     * @return string
     */
    public function getService()
    {
        if (!$this->service) {
            throw new \LogicException('Annotation service name is required');
        }

        return $this->service;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        if (!$this->method) {
            throw new \LogicException('Annotation method name is required');
        }

        return $this->method;
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        if (!$this->property) {
            throw new \LogicException('Annotation property name is required');
        }

        return $this->property;
    }

    /**
     * @return string
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}
