<?php
/**
 * File ClassFileParser.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Parser;

use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ClassFileParser
 *
 * @package Pfrembot\RestProxyBundle\Parser
 */
class ClassFileParser
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var NodeTraverser
     */
    private $traverser;

    /**
     * ClassFileParser constructor
     *
     * @param Parser $parser
     * @param NodeTraverser $traverser
     */
    public function __construct(Parser $parser, NodeTraverser $traverser)
    {
        $this->parser = $parser;
        $this->traverser = $traverser;
    }

    /**
     * Return class name from file
     *
     * @param SplFileInfo $file
     * @return null|string
     */
    public function parse(SplFileInfo $file)
    {
        $resolver = new NodeVisitor\ClassResolver();

        $nodes = $this->parser->parse($file->getContents());

        $this->traverser->addVisitor($resolver);
        $this->traverser->traverse($nodes);
        $this->traverser->removeVisitor($resolver);

        return $resolver->getClassName();
    }
}
