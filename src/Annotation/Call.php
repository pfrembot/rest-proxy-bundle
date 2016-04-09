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
 *
 * @package Pfrembot\RestProxyBundle\Annotation
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
     * Return proxy service
     *
     * Proxy service to use for loading embedded class data on
     * the target property
     *
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
     * Return proxy service method
     *
     * This is the method to be called on the proxied service
     * for LAZY loading entity values
     *
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
     * Return property
     *
     * Class property to be hydrated if the property is
     * currently falsy on the entity
     *
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
     * Return proxy call method arguments array string
     *
     * This must return PHP array string syntax so it can be parsed by
     * the PhpParser during proxy class building
     *
     * @return string
     */
    public function getArguments()
    {
        if (!is_string($this->arguments)) {
            throw new \LogicException(
                'Must be PHP array syntax string (e.g. "[$this->id]") to be parsed during proxy class builder'
            );
        }

        return $this->arguments;
    }
}
