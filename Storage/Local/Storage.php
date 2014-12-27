<?php
namespace Poirot\Filesystem\Storage\Local;

use Poirot\Core\AbstractOptions;
use Poirot\Core\Interfaces\OptionsProviderInterface;
use Poirot\Filesystem\Interfaces\iFile;
use Poirot\Filesystem\Interfaces\iDirectory;
use Poirot\Filesystem\Interfaces\iLink;
use Poirot\Filesystem\Interfaces\iCommon;
use Poirot\Filesystem\Storage\AbstractStorage;

class Storage extends AbstractStorage implements
    OptionsProviderInterface
{
    /**
     * @var Options
     */
    protected $options;

    /**
     * Implemented in AbstractStorage Construct
     */
    function consIt()
    {
        $location = $this->options()->getRootDir();
        if (!is_dir($location))
            mkdir($location, 0755, true);
    }

    /**
     * @return Options
     */
    function options()
    {
        return ($this->options)
            ?: $this->options = new Options();
    }

    /**
     * Current Working Directory
     *
     * - right trimmed with Directory Separator
     * - storage with empty working directory
     *   mean the base storage
     * - with creating files or folder cwd will
     *   append as path
     *
     * @return string
     */
    function getCwd()
    {
        return '';
    }

    /**
     * List Contents
     *
     * @return array[iFile|iLink|iDirectory]
     */
    function lsContent()
    {
        $result = [];
        $location = $this->options()->getRootDir();
        $iterator = new \DirectoryIterator($location);
        foreach ($iterator as $fileinfo) {
            $path = $fileinfo->getPath();
            if (preg_match('#(^|/)\.{1,2}$#', $path))
                continue;

            $result[] = $this->createFromPath($path);
        }

        return $result;
    }

    /**
     * Create new Folder Instance
     *
     * @return iDirectory
     */
    function dir()
    {
        // TODO: Implement dir() method.
    }

    /**
     * Create new File Instance
     *
     * @return iFile
     */
    function file()
    {
        // TODO: Implement file() method.
    }

    /**
     * Create new Link Instance
     *
     * @return iLink
     */
    function link()
    {
        // TODO: Implement link() method.
    }

    /**
     * Open Existence File Or Folder
     *
     * @param iCommon $node File/Folder
     *
     * @throws \Exception
     * @return iCommon|iFile|iLink
     */
    function open(iCommon $node)
    {
        if (!$node ->isExists())
            throw new \Exception('Node not exists to open.');

        return $node;
    }

    /**
     * Write File To Storage
     *
     * @param iCommon|iFile|iDirectory|iLink $node File
     *
     * @return $this
     */
    function write(iCommon $node)
    {
        $node->setPath(
            $this->getCwd()
            . self::DS .
            $node->getPath()
        );

        $node->mkIfNotExists() or function() {
            throw new \Exception('File Exists');
        };
    }

    /**
     * Create File Or Folder From Given Path
     *
     * - if not exists
     *   name without extension considered as folder
     *   else this is file
     * - if exists
     *   check type of current node and make object
     *
     * @param string $path Path
     *
     * @throws \Exception
     * @return mixed|boolean
     */
    function createFromPath($path)
    {
        if ($path === null || $path === '')
            throw new \Exception('Path Cant be empty.');

        $return = false;
        $pathinfo = pathinfo($path);
        if (file_exists($path))
            switch (@filetype($path)) {
                case 'link':
                    $return = new Link();
                    break;
                case 'dir':
                    $return = new Directory();
                    break;
                case 'file':
                    $return = new File();
                    break;
            }

        if (!$return)
            if (array_key_exists('extension', $pathinfo))
                $return = new File();
            elseif (array_key_exists('basename', $pathinfo))
                $return = new Directory();

        return $return;
    }
}
 