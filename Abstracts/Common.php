<?php
namespace Poirot\Filesystem\Abstracts;

use Poirot\Core\BuilderSetterTrait;
use Poirot\Filesystem\Interfaces\iFilesystem;
use Poirot\Filesystem\Interfaces\iFilesystemAware;
use Poirot\Filesystem\Interfaces\iFilesystemProvider;
use Poirot\Filesystem\Local\Filesystem as LocalFilesystem;
use Poirot\Filesystem\Util;

abstract class Common
    implements
    iFilesystemAware,
    iFilesystemProvider
{
    use BuilderSetterTrait;

    /**
     * @var iFilesystem
     */
    protected $filesystem;

    protected $basename;
    protected $path;

    /**
     * Construct
     *
     * @param array|string $setterBuilder ArraySetter or extracted info from path
     */
    function __construct($setterBuilder = null)
    {
        if (is_string($setterBuilder))
            $setterBuilder = Util::getPathInfo($setterBuilder);

        if (is_array($setterBuilder))
           $this->setupFromArray($setterBuilder);
    }

    /**
     * Set Basename of file or folder
     *
     * ! without extension
     *
     * - /path/to/filename[.ext]
     * - /path/to/folderName/
     *
     * @param string $name Basename
     *
     * @return $this
     */
    function setBasename($name)
    {
        $this->basename = $name;

        return $this;
    }

    /**
     * Gets the base name of the file
     *
     * - Without extension on files
     *
     * @return string
     */
    function getBasename()
    {
        return $this->basename;
    }

    /**
     * Set Path
     *
     * - trimmed left /\ path
     * - it's consumed from cwd of filesystem or storage
     *
     * @param string|null $path Path To File/Folder
     *
     * @return $this
     */
    function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Gets the path without filename
     *
     * - Get CWDir (Filesystem) If Path Not Set
     *
     * @return string
     */
    function getPath()
    {
        if ($this->path === null)
            $this->setPath(
                $this->filesystem()->getCwd()->getRealPathName()
            );

        return Util::normalizePath($this->path);
    }

    /**
     * Set Filesystem
     *
     * @param iFilesystem $filesystem
     *
     * @return $this
     */
    function setFilesystem(iFilesystem $filesystem)
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    /**
     * @return iFilesystem
     */
    function Filesystem()
    {
        if (!$this->filesystem)
            $this->filesystem = new LocalFilesystem();

        return $this->filesystem;
    }
}
