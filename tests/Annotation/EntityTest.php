<?php
/**
 * File EntityTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */

namespace Pfrembot\RestProxyBundle\Tests\Annotation;

use Pfrembot\RestProxyBundle\Annotation as RestProxy;

/**
 * Class EntityTest
 *
 * @coversDefaultClass Pfrembot\RestProxyBundle\Annotation\Entity
 *
 * @package Pfrembot\RestProxyBundle\Tests\Annotation
 */
class EntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $annotation = new RestProxy\Entity([]);

        $this->assertInstanceOf(RestProxy\Entity::class, $annotation);
    }
}
