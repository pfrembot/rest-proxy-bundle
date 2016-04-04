<?php
/**
 * File ClassFinder.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Finder;

use Doctrine\Common\Annotations\Reader;
use Pfrembot\RestProxyBundle\Annotation as RestProxy;
use Pfrembot\RestProxyBundle\Parser\ClassFileParser;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ProxyClassFinder
 *
 * @package Pfrembot\RestProxyBundle\Finder
 */
class ProxyClassFinder
{
    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var ClassFileParser
     */
    private $parser;

    /**
     * ClassFinder constructor
     *
     * @param Finder $finder
     * @param Reader $reader
     * @param ClassFileParser $parser
     */
    public function __construct(Finder $finder, Reader $reader, ClassFileParser $parser)
    {
        $this->finder = $finder;
        $this->reader = $reader;
        $this->parser = $parser;
    }

    /**
     * Return all proxy class names form src directory
     *
     * @return array
     */
    public function getAllClassNames()
    {
        $classNames = [];

        /** @var SplFileInfo $file */
        foreach ($this->finder as $file) {
            if (!$className = $this->parser->parse($file)) {
                continue;
            }

            $reflection = new \ReflectionClass($className);
            $annotation = $this->reader->getClassAnnotation($reflection, RestProxy\Entity::class);

            if (!$annotation) {
                continue;
            }

            array_push($classNames, $className);
        }

        return $classNames;
    }
}
