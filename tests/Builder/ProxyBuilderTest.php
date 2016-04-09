<?php
/**
 * File ProxyBuilderTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Tests\Builder;

use Doctrine\Common\Annotations\Reader;
use Pfrembot\RestProxyBundle\Annotation as RestProxy;
use Pfrembot\RestProxyBundle\Builder\ProxyBuilder;
use PhpParser\Builder;
use PhpParser\BuilderFactory;
use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\PrettyPrinter;

/**
 * Class ProxyBuilderTest
 *
 * @coversDefaultClass Pfrembot\RestProxyBundle\Builder\ProxyBuilder
 *
 * @package Pfrembot\RestProxyBundle\Tests\Builder
 */
class ProxyBuilderTest extends\PHPUnit_Framework_TestCase
{
    /**
     * @var Reader|\Mockery\MockInterface
     */
    private $reader;

    /**
     * @var PrettyPrinter\Standard
     */
    private $printer;

    /**
     * @var ProxyBuilder
     */
    private $builder;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->reader = \Mockery::mock(Reader::class);

        $this->printer = new PrettyPrinter\Standard();
        $this->builder = new ProxyBuilder($this->reader);
    }

    /**
     * Trim php file content whitespace for
     * easier string comparison
     *
     * @param string $string
     * @return string
     */
    private function trimWhitespace($string)
    {
        return preg_replace('/\s+/', ' ', $string);
    }

    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertAttributeInstanceOf(BuilderFactory::class, 'factory', $this->builder);
        $this->assertAttributeInstanceOf(Parser::class, 'parser', $this->builder);
        $this->assertAttributeInstanceOf(Reader::class, 'reader', $this->builder);
    }

    /**
     * @covers ::build
     * @covers ::buildClass
     * @covers ::buildProxyMethod
     */
    public function testBuildWithCall()
    {
        $reflectionClass = \Mockery::mock(\ReflectionClass::class);
        $reflectionMethod = \Mockery::mock(\ReflectionMethod::class);

        $annotation = new RestProxy\Call(['property' => 'bar', 'service' => 'bar.service', 'method' => 'getBar', 'arguments' => '[$this->id]']);

        $reflectionClass->shouldReceive('getMethods')->once()->with(\ReflectionMethod::IS_PUBLIC)->andReturn([$reflectionMethod]);
        $reflectionClass->shouldReceive('getShortName')->once()->withNoArgs()->andReturn('Foo');
        $reflectionClass->shouldReceive('getNamespaceName')->once()->withNoArgs()->andReturn('Some\\NameSpace');
        $reflectionClass->shouldReceive('getName')->once()->withNoArgs()->andReturn('Some\\NameSpace\\Foo');

        $reflectionMethod->shouldReceive('getParameters')->once()->withNoArgs()->andReturn([]);
        $reflectionMethod->shouldReceive('getName')->twice()->withNoArgs()->andReturn('getBar');

        $this->reader->shouldReceive('getClassAnnotation')->once()->with($reflectionClass, RestProxy\Entity::class)->andReturn(true);
        $this->reader->shouldReceive('getMethodAnnotation')->once()->with($reflectionMethod, RestProxy\Call::class)->andReturn($annotation);

        $proxyClass = $this->builder->build($reflectionClass);

        $this->assertInstanceOf(Builder\Namespace_::class, $proxyClass);
        $this->assertEquals('RestProxy\\Some\\NameSpace', (string) $proxyClass->getNode()->name);
        $this->assertEquals('Foo', (string) $proxyClass->getNode()->stmts[2]->name);
        $this->assertEquals(
            $this->trimWhitespace($this->printer->prettyPrintFile([$proxyClass->getNode()])),
            '<?php namespace RestProxy\Some\NameSpace; use JMS\Serializer\Annotation as JMS; use Some\NameSpace\Foo as BaseClass; /** ProxyClass */ final class Foo extends BaseClass implements \Pfrembot\RestProxyBundle\Proxy\ProxyInterface { /** @JMS\Exclude() */ private $__container__; /** ProxyInitializer */ public function __initialize__(\Symfony\Component\DependencyInjection\ContainerInterface $container) { $this->__container__ = $container; } /** ProxyMethod */ public function getBar() { if (!$this->bar) { $this->bar = call_user_func_array(array($this->__container__->get(\'bar.service\'), \'getBar\'), array($this->id)); } return call_user_func_array(\'parent::getBar\', func_get_args()); } }'
        );
    }

    /**
     * @covers ::build
     * @covers ::buildClass
     * @covers ::buildProxyMethod
     * @covers ::addLinkDictionary
     */
    public function testBuildWithLink()
    {
        $reflectionClass = \Mockery::mock(\ReflectionClass::class);
        $reflectionMethod = \Mockery::mock(\ReflectionMethod::class);

        $annotation = new RestProxy\Link(['value' => 'foo', 'service' => 'foo.service', 'method' => 'getFoo']);

        $reflectionClass->shouldReceive('getMethods')->once()->with(\ReflectionMethod::IS_PUBLIC)->andReturn([$reflectionMethod]);
        $reflectionClass->shouldReceive('getShortName')->once()->withNoArgs()->andReturn('Foo');
        $reflectionClass->shouldReceive('getNamespaceName')->once()->withNoArgs()->andReturn('Some\\NameSpace');
        $reflectionClass->shouldReceive('getName')->once()->withNoArgs()->andReturn('Some\\NameSpace\\Foo');

        $reflectionMethod->shouldReceive('getParameters')->once()->withNoArgs()->andReturn([]);
        $reflectionMethod->shouldReceive('getName')->twice()->withNoArgs()->andReturn('getFoo');

        $this->reader->shouldReceive('getClassAnnotation')->once()->with($reflectionClass, RestProxy\Entity::class)->andReturn(true);
        $this->reader->shouldReceive('getMethodAnnotation')->once()->with($reflectionMethod, RestProxy\Call::class)->andReturn($annotation);

        $proxyClass = $this->builder->build($reflectionClass);

        $this->assertInstanceOf(Builder\Namespace_::class, $proxyClass);
        $this->assertEquals('RestProxy\\Some\\NameSpace', (string) $proxyClass->getNode()->name);
        $this->assertEquals('Foo', (string) $proxyClass->getNode()->stmts[2]->name);
        $this->assertEquals(
            $this->trimWhitespace($this->printer->prettyPrintFile([$proxyClass->getNode()])),
            '<?php namespace RestProxy\Some\NameSpace; use JMS\Serializer\Annotation as JMS; use Some\NameSpace\Foo as BaseClass; /** ProxyClass */ final class Foo extends BaseClass implements \Pfrembot\RestProxyBundle\Proxy\ProxyInterface { use \Pfrembot\RestProxyBundle\Mixin\LinkDictionaryTrait; /** @JMS\Exclude() */ private $__container__; /** ProxyInitializer */ public function __initialize__(\Symfony\Component\DependencyInjection\ContainerInterface $container) { $this->__container__ = $container; } /** ProxyMethod */ public function getFoo() { if (!$this->foo) { $this->foo = call_user_func_array(array($this->__container__->get(\'foo.service\'), \'getFoo\'), array($this->getLink(\'foo\'))); } return call_user_func_array(\'parent::getFoo\', func_get_args()); } }'
        );
    }

    /**
     * @covers ::build
     * @covers ::buildClass
     * @covers ::buildProxyMethod
     * @covers ::addLinkDictionary
     */
    public function testBuildWithLinks()
    {
        $reflectionClass = \Mockery::mock(\ReflectionClass::class);
        $reflectionMethod = \Mockery::mock(\ReflectionMethod::class);

        $link1 = new RestProxy\Link(['value' => 'foo', 'service' => 'foo.service', 'method' => 'getFoo']);
        $link2 = new RestProxy\Link(['value' => 'bar', 'service' => 'bar.service', 'method' => 'getBar']);

        $reflectionClass->shouldReceive('getMethods')->once()->with(\ReflectionMethod::IS_PUBLIC)->andReturn([$reflectionMethod, $reflectionMethod]);
        $reflectionClass->shouldReceive('getShortName')->once()->withNoArgs()->andReturn('Foo');
        $reflectionClass->shouldReceive('getNamespaceName')->once()->withNoArgs()->andReturn('Some\\NameSpace');
        $reflectionClass->shouldReceive('getName')->once()->withNoArgs()->andReturn('Some\\NameSpace\\Foo');

        $reflectionMethod->shouldReceive('getParameters')->twice()->withNoArgs()->andReturn([]);
        $reflectionMethod->shouldReceive('getName')->twice()->withNoArgs()->andReturn('getFoo');
        $reflectionMethod->shouldReceive('getName')->twice()->withNoArgs()->andReturn('getBar');

        $this->reader->shouldReceive('getClassAnnotation')->once()->with($reflectionClass, RestProxy\Entity::class)->andReturn(true);
        $this->reader->shouldReceive('getMethodAnnotation')->once()->with($reflectionMethod, RestProxy\Call::class)->andReturn($link1);
        $this->reader->shouldReceive('getMethodAnnotation')->once()->with($reflectionMethod, RestProxy\Call::class)->andReturn($link2);

        $proxyClass = $this->builder->build($reflectionClass);

        $this->assertInstanceOf(Builder\Namespace_::class, $proxyClass);
        $this->assertEquals('RestProxy\\Some\\NameSpace', (string) $proxyClass->getNode()->name);
        $this->assertEquals('Foo', (string) $proxyClass->getNode()->stmts[2]->name);
        $this->assertEquals(1, substr_count(
            $this->printer->prettyPrintFile([$proxyClass->getNode()]),
            'use \\Pfrembot\\RestProxyBundle\\Mixin\\LinkDictionaryTrait;')
        );
        $this->assertEquals(
            $this->trimWhitespace($this->printer->prettyPrintFile([$proxyClass->getNode()])),
            '<?php namespace RestProxy\Some\NameSpace; use JMS\Serializer\Annotation as JMS; use Some\NameSpace\Foo as BaseClass; /** ProxyClass */ final class Foo extends BaseClass implements \Pfrembot\RestProxyBundle\Proxy\ProxyInterface { use \Pfrembot\RestProxyBundle\Mixin\LinkDictionaryTrait; /** @JMS\Exclude() */ private $__container__; /** ProxyInitializer */ public function __initialize__(\Symfony\Component\DependencyInjection\ContainerInterface $container) { $this->__container__ = $container; } /** ProxyMethod */ public function getFoo() { if (!$this->foo) { $this->foo = call_user_func_array(array($this->__container__->get(\'foo.service\'), \'getFoo\'), array($this->getLink(\'foo\'))); } return call_user_func_array(\'parent::getFoo\', func_get_args()); } /** ProxyMethod */ public function getBar() { if (!$this->bar) { $this->bar = call_user_func_array(array($this->__container__->get(\'bar.service\'), \'getBar\'), array($this->getLink(\'bar\'))); } return call_user_func_array(\'parent::getBar\', func_get_args()); } }'
        );
    }

    /**
     * @covers ::build
     * @covers ::buildClass
     * @covers ::buildProxyMethod
     */
    public function testBuildWithProxyMethodArgs()
    {
        $reflectionClass = \Mockery::mock(\ReflectionClass::class);
        $reflectionMethod = \Mockery::mock(\ReflectionMethod::class);
        $reflectionParameter = \Mockery::mock(\ReflectionParameter::class);

        $annotation = new RestProxy\Call(['property' => 'bar', 'service' => 'bar.service', 'method' => 'getBar', 'arguments' => '[$this->id]']);

        $reflectionClass->shouldReceive('getMethods')->once()->with(\ReflectionMethod::IS_PUBLIC)->andReturn([$reflectionMethod]);
        $reflectionClass->shouldReceive('getShortName')->once()->withNoArgs()->andReturn('Foo');
        $reflectionClass->shouldReceive('getNamespaceName')->once()->withNoArgs()->andReturn('Some\\NameSpace');
        $reflectionClass->shouldReceive('getName')->once()->withNoArgs()->andReturn('Some\\NameSpace\\Foo');

        $reflectionMethod->shouldReceive('getParameters')->once()->withNoArgs()->andReturn([$reflectionParameter]);
        $reflectionMethod->shouldReceive('getName')->twice()->withNoArgs()->andReturn('getBar');

        $reflectionParameter->shouldReceive('getName')->once()->withNoArgs()->andReturn('arg1');

        $this->reader->shouldReceive('getClassAnnotation')->once()->with($reflectionClass, RestProxy\Entity::class)->andReturn(true);
        $this->reader->shouldReceive('getMethodAnnotation')->once()->with($reflectionMethod, RestProxy\Call::class)->andReturn($annotation);

        $proxyClass = $this->builder->build($reflectionClass);

        $this->assertInstanceOf(Builder\Namespace_::class, $proxyClass);
        $this->assertEquals('RestProxy\\Some\\NameSpace', (string) $proxyClass->getNode()->name);
        $this->assertEquals('Foo', (string) $proxyClass->getNode()->stmts[2]->name);
        $this->assertEquals(
            $this->trimWhitespace($this->printer->prettyPrintFile([$proxyClass->getNode()])),
            '<?php namespace RestProxy\Some\NameSpace; use JMS\Serializer\Annotation as JMS; use Some\NameSpace\Foo as BaseClass; /** ProxyClass */ final class Foo extends BaseClass implements \Pfrembot\RestProxyBundle\Proxy\ProxyInterface { /** @JMS\Exclude() */ private $__container__; /** ProxyInitializer */ public function __initialize__(\Symfony\Component\DependencyInjection\ContainerInterface $container) { $this->__container__ = $container; } /** ProxyMethod */ public function getBar($arg1) { if (!$this->bar) { $this->bar = call_user_func_array(array($this->__container__->get(\'bar.service\'), \'getBar\'), array($this->id)); } return call_user_func_array(\'parent::getBar\', func_get_args()); } }'
        );
    }

    /**
     * @covers ::build
     * @covers ::buildClass
     * @covers ::buildProxyMethod
     */
    public function testBuildWithNoProxyMethods()
    {
        $reflectionClass = \Mockery::mock(\ReflectionClass::class);
        $reflectionMethod = \Mockery::mock(\ReflectionMethod::class);

        $reflectionClass->shouldReceive('getMethods')->once()->with(\ReflectionMethod::IS_PUBLIC)->andReturn([$reflectionMethod]);
        $reflectionClass->shouldReceive('getShortName')->once()->withNoArgs()->andReturn('Foo');
        $reflectionClass->shouldReceive('getNamespaceName')->once()->withNoArgs()->andReturn('Some\\NameSpace');
        $reflectionClass->shouldReceive('getName')->once()->withNoArgs()->andReturn('Some\\NameSpace\\Foo');

        $this->reader->shouldReceive('getClassAnnotation')->once()->with($reflectionClass, RestProxy\Entity::class)->andReturn(true);
        $this->reader->shouldReceive('getMethodAnnotation')->once()->with($reflectionMethod, RestProxy\Call::class)->andReturnNull();

        $proxyClass = $this->builder->build($reflectionClass);

        $this->assertInstanceOf(Builder\Namespace_::class, $proxyClass);
        $this->assertEquals('RestProxy\\Some\\NameSpace', (string) $proxyClass->getNode()->name);
        $this->assertEquals('Foo', (string) $proxyClass->getNode()->stmts[2]->name);
        $this->assertEquals(
            $this->trimWhitespace($this->printer->prettyPrintFile([$proxyClass->getNode()])),
            '<?php namespace RestProxy\Some\NameSpace; use JMS\Serializer\Annotation as JMS; use Some\NameSpace\Foo as BaseClass; /** ProxyClass */ final class Foo extends BaseClass implements \Pfrembot\RestProxyBundle\Proxy\ProxyInterface { /** @JMS\Exclude() */ private $__container__; /** ProxyInitializer */ public function __initialize__(\Symfony\Component\DependencyInjection\ContainerInterface $container) { $this->__container__ = $container; } }'
        );
    }

    /**
     * @covers ::build
     */
    public function testBuildWithNoClassAnnotation()
    {
        $reflectionClass = \Mockery::mock(\ReflectionClass::class);

        $reflectionClass->shouldNotReceive('getMethods');
        $reflectionClass->shouldNotReceive('getShortName');
        $reflectionClass->shouldNotReceive('getNamespaceName');
        $reflectionClass->shouldNotReceive('getName');

        $this->reader->shouldReceive('getClassAnnotation')->once()->with($reflectionClass, RestProxy\Entity::class)->andReturn(false);
        $this->reader->shouldNotReceive('getMethodAnnotation');

        $this->assertFalse($this->builder->build($reflectionClass));
    }
}
