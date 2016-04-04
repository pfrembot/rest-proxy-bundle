<?php
/**
 * File ClassResolver.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Parser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\NodeVisitorAbstract;

/**
 * Class ClassResolver
 *
 * @package Pfrembot\RestProxyBundle\Parser\NodeVisitor
 */
class ClassResolver extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $namespace;

    /**
     * {@inheritdoc}
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Stmt\Class_) {
            $this->class = $node->name;
        }

        if ($node instanceof Stmt\Namespace_) {
            $this->namespace = $node->name;
        }
    }

    /**
     * Return parsed classname
     *
     * @return null|string
     */
    public function getClassName()
    {
        if (!$this->namespace || !$this->class) {
            return null;
        }

        return sprintf('%s\\%s', $this->namespace, $this->class);
    }
}
