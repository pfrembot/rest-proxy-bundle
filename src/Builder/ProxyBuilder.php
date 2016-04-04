<?php
/**
 * File ProxyBuilder.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Builder;

use Doctrine\Common\Annotations\Reader;
use PhpParser\Builder;
use PhpParser\BuilderFactory;
use PhpParser\Parser;
use PhpParser\PrettyPrinter;
use Pfrembot\RestProxyBundle\Annotation as RestProxy;
use Pfrembot\RestProxyBundle\Cache\ProxyCache;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProxyBuilder
{
    /**
     * @var BuilderFactory
     */
    private $factory;

    /**
     * @var PrettyPrinter\Standard
     */
    private $printer;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * Proxy method body template
     *
     * @var string
     */
    private $proxyMethodBody = '<?php
        if (!$this->%2$s) {
            $this->%2$s = call_user_func_array([$this->__container__->get(\'%3$s\'), \'%4$s\'], %5$s);
        }

        return call_user_func_array(\'parent::%1$s\', func_get_args());
    ?>';

    /**
     * Initializer method body
     *
     * @var string
     */
    private $initializerMethodBody = '<?php
        $this->__container__ = $container;
    ?>';

    /**
     * ProxyBuilder constructor
     *
     * @param Parser $parser
     * @param Reader $reader
     */
    public function __construct(Parser $parser, Reader $reader)
    {
        $this->factory = new BuilderFactory();
        $this->printer = new PrettyPrinter\Standard();
        $this->parser = $parser;
        $this->reader = $reader;
    }

    /**
     * Return builder class model
     *
     * @param \ReflectionClass $reflection
     * @return Builder\Class_|Builder\Namespace_
     */
    public function build(\ReflectionClass $reflection)
    {
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        $classModel = $this->buildClass($reflection);

        foreach ($methods as $method) {
            $this->buildProxyMethod($classModel, $method);
        }

        return $this->factory
            ->namespace(sprintf('%s\\%s', ProxyCache::NAMESPACE_PREFIX, $reflection->getNamespaceName()))
            ->addStmt($this->factory->use('JMS\Serializer\Annotation')->as('JMS'))
            ->addStmt($this->factory->use($reflection->getName())->as('BaseClass'))
            ->addStmt($classModel)
        ;
    }

    /**
     * Return base builder class model
     *
     * @param \ReflectionClass $reflection
     * @return Builder\Class_
     */
    private function buildClass(\ReflectionClass $reflection)
    {
        return $this->factory->class($reflection->getShortName())
            ->extend('BaseClass')
            ->makeFinal()
            ->setDocComment('/** ProxyClass */')
            ->addStmt($this->factory->property('__container__')
                ->makePrivate()
                ->setDocComment('/** @JMS\Exclude() */')
            )
            ->addStmt($this->factory->method('__initialize__')
                ->makePublic()
                ->addParam($this->factory->param('container')->setTypeHint('\\' . ContainerInterface::class))
                ->setDocComment('/** ProxyInitializer */')
                ->addStmts($this->parser->parse($this->initializerMethodBody))
            )
        ;
    }

    /**
     * Add proxy method to class model
     *
     * @param Builder\Class_ $class
     * @param \ReflectionMethod $method
     * @return Builder\Class_|false
     */
    private function buildProxyMethod(Builder\Class_ $class, \ReflectionMethod $method)
    {
        /** @var RestProxy\Call $annotation */
        $annotation = $this->reader->getMethodAnnotation($method, RestProxy\Call::class);

        if (!$annotation) {
            return false;
        }

        $parameters = array_map(function(\ReflectionParameter $parameter) {
            return $this->factory->param($parameter->getName());
        }, $method->getParameters());

        return $class->addStmt($this->factory->method($method->getName())
            ->makePublic()
            ->addParams($parameters)
            ->setDocComment('/** ProxyMethod */')
            ->addStmts($this->parser->parse(sprintf($this->proxyMethodBody,
                $method->getName(),
                $annotation->getProperty(),
                $annotation->getService(),
                $annotation->getMethod(),
                $annotation->getArguments()
            )))
        );
    }
}