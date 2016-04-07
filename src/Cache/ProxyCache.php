<?php
/**
 * File ProxyCache.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Pfrembot\RestProxyBundle\Cache;

use PhpParser\Builder;
use PhpParser\BuilderAbstract;
use PhpParser\PrettyPrinterAbstract;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ProxyCache
 *
 * @package Pfrembot\RestProxyBundle\Cache
 */
class ProxyCache
{
    const CACHE_PATH = 'restproxy';
    const NAMESPACE_PREFIX = 'RestProxy';

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var PrettyPrinterAbstract
     */
    private $printer;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * ProxyCache constructor
     *
     * @param PrettyPrinterAbstract $printer
     * @param Filesystem $fs
     * @param $cacheDir
     */
    public function __construct(PrettyPrinterAbstract $printer, Filesystem $fs, $cacheDir)
    {
        $this->fs = $fs;
        $this->printer = $printer;
        $this->cacheDir = $cacheDir . DIRECTORY_SEPARATOR . self::CACHE_PATH;

        // make missing cache directory
        if (!$this->fs->exists($this->cacheDir)) {
            $this->fs->mkdir($this->cacheDir, 0775);
        }
    }

    /**
     * Write proxy class to disk
     *
     * @param BuilderAbstract|Builder\Namespace_ $model
     * @param string $name
     */
    public function write(BuilderAbstract $model, $name)
    {
        $node = $model->getNode();
        $file = $this->cacheDir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $name);

        $content = $this->printer->prettyPrintFile([$node]);

        $this->fs->dumpFile(sprintf('%s.php', $file), $content);
    }
}
