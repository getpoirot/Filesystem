<?php
namespace Poirot\Filesystem\Adapter;

use Poirot\Filesystem\Adapter\Local\LocalFS;
use Poirot\Filesystem\Interfaces\Filesystem\iCommonInfo;
use Poirot\Filesystem\Interfaces\iFilesystem;
use Poirot\Filesystem\Interfaces\iFilesystemAware;
use Poirot\Filesystem\Interfaces\iFilesystemProvider;
use Poirot\PathUri\Interfaces\iPathFileUri;
use Poirot\PathUri\PathFileUri;

abstract class AbstractCommonNode
    implements
    iCommonInfo,
    iFilesystemAware,
    iFilesystemProvider
{
    /**
     * @var iFilesystem
     */
    protected $filesystem;

    /**
     * @var iPathFileUri
     */
    protected $pathUri;

    /**
     * Construct
     *
     * @param array|string|iPathFileUri $pathUri
     * @throws \Exception
     */
    function __construct($pathUri = null)
    {
        if ($pathUri instanceof iPathFileUri)
            $pathUri = $pathUri->toArray();
        elseif (is_string($pathUri))
            $pathUri = $this->pathUri()->parse($pathUri);

        if ($pathUri !== null) {
            if (is_array($pathUri))
                $this->pathUri()->fromArray($pathUri);
            else
                throw new \Exception(sprintf(
                    'PathUri must be instanceof iPathFileUri, Array or String, given: %s'
                    , is_object($pathUri) ? get_class($pathUri) : gettype($pathUri)
                ));
        }
    }

    /**
     * Get Path Uri Filename
     *
     * - it used to build uri address to file
     *
     * @return iPathFileUri
     */
    function pathUri()
    {
        if (!$this->pathUri)
            $this->pathUri = (new PathFileUri)
                // by default create relative paths
                ->setPathStrMode(PathFileUri::PATH_AS_RELATIVE)
                ->setSeparator(
                    $this->filesystem()->pathUri()
                        ->getSeparator()
                )
            ;

        return $this->pathUri;
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
            $this->filesystem = new LocalFS();

        return $this->filesystem;
    }
}
