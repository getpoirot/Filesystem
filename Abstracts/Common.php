<?php
namespace Poirot\Filesystem\Abstracts;

use Poirot\Filesystem\Interfaces\Filesystem\iFSPathUri;
use Poirot\Filesystem\Interfaces\iFilesystem;
use Poirot\Filesystem\Interfaces\iFilesystemAware;
use Poirot\Filesystem\Interfaces\iFilesystemProvider;
use Poirot\Filesystem\Local\Filesystem as LocalFilesystem;

abstract class Common
    implements
    iFilesystemAware,
    iFilesystemProvider
{
    /**
     * @var iFilesystem
     */
    protected $filesystem;

    /**
     * @var iFSPathUri
     */
    protected $filepath;

    /**
     * internal usage to build filepath object
     *
     * @var string|array
     */
    protected $_pathuri;

    /**
     * Construct
     *
     * @param array|string $pathBuilder ArraySetter or extracted info from path
     */
    function __construct($pathBuilder = null)
    {
        $this->_pathuri = $pathBuilder;
    }

    /**
     * Get Path Uri Filename
     *
     * - it used to build uri address to file
     *
     * @return iFSPathUri
     */
    function filePath()
    {
        if (!$this->filepath)
            $this->filepath = new FSPathUri($this->_pathuri);

        return $this->filepath;
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
    function filesystem()
    {
        if (!$this->filesystem)
            $this->filesystem = new LocalFilesystem();

        return $this->filesystem;
    }
}
