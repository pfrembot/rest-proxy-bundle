<?php
/**
 * File Entity.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Entity
 *
 * Designates the class as a Hateoas API entity
 *
 * @Annotation
 * @Target("CLASS")
 * @package Pfrembot\RestProxyBundle\Annotation
 */
class Entity extends Annotation
{

}
