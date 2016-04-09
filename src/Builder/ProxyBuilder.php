<?php
/**
 * File ProxyBuilder.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Builder;

use Doctrine\Common\Annotations\Reader;
use Pfrembot\RestProxyBundle\Mixin\LinkDictionaryTrait;
use PhpParser\Builder;
use PhpParser\BuilderFactory;
use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\Parser;
use Pfrembot\RestProxyBundle\Annotation as RestProxy;
use Pfrembot\RestProxyBundle\Cache\ProxyCache;
use Pfrembot\RestProxyBundle\Proxy\ProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ProxyBuilder
 *
 * @package Pfrembot\RestProxyBundle\Builder
 */
class ProxyBuilder
{
    /**
     * @var BuilderFactory
     */
    private $factory;

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
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->factory = new BuilderFactory();
        $this->parser = new Parser(new Lexer());
        $this->reader = $reader;
    }

    /**
     * Return builder class model
     *
     * @param \ReflectionClass $reflection
     * @return Builder\Namespace_|false
     */
    public function build(\ReflectionClass $reflection)
    {
        $annotation = $this->reader->getClassAnnotation($reflection, RestProxy\Entity::class);

        if (!$annotation) {
            return false;
        }

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
            ->implement('\\'.ProxyInterface::class)
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

        if ($annotation instanceof RestProxy\Link) {
            $this->addLinkDictionary($class);
        }

        $parameters = array_map(function(\ReflectionParameter $parameter) {
            // @todo: add support for default parameter values
            // @todo: add support for type hinted parameters
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

    /**
     * Add a single link dictionary trait statement to
     * the class model
     *
     * @param Builder\Class_ $class
     * @reutnr void
     */
    private function addLinkDictionary(Builder\Class_ $class)
    {
        foreach ($class->getNode()->stmts as $stmt) {
            if ($stmt instanceof  Node\Stmt\TraitUse) {
                return;
            }
        }

        $class->addStmt(new Node\Stmt\TraitUse([
            new Node\Name('\\' . LinkDictionaryTrait::class),
        ]));
    }
}
