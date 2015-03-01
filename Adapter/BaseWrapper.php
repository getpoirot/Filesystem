<?php
namespace Poirot\Filesystem\Adapter;

use Poirot\Filesystem\Interfaces\Filesystem\iCommon;
use Poirot\Filesystem\Interfaces\Filesystem\iCommonInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iDirectoryInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iFile;
use Poirot\Filesystem\Interfaces\Filesystem\iFileInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iFilePermissions;
use Poirot\Filesystem\Interfaces\Filesystem\iLinkInfo;
use Poirot\Filesystem\Interfaces\iFilesystem;
use Poirot\Filesystem\Interfaces\iFsBase;
use Poirot\PathUri\Interfaces\iPathFileUri;

/**
 * @method string getFilename(iCommonInfo $file)
 * @method string getFileExtension(iFileInfo $file)
 * @method string getBasename(iCommonInfo $file)
 * @method $this chgrp(iCommonInfo $file, $group)
 * @method $this chmod(iCommonInfo $file, iFilePermissions $mode)
 * @method $this chown(iCommonInfo $file, $user)
 * @method $this copy(iCommonInfo $source, iCommon $dest)
 * @method mixed getFreeSpace()
 * @method mixed getTotalSpace()
 * @method $this putFileContents(iFile $file, $contents)
 * @method $this flock(iFileInfo $file, $lock = LOCK_EX)
 * @method bool isWritable(iCommonInfo $file)
 * @method $this mkLink(iLinkInfo $link)
 * @method $this mkDir(iDirectoryInfo $dir, iFilePermissions $mode)
 * @method $this rename(iCommonInfo $file, $newName)
 * @method $this rmDir(iDirectoryInfo $dir)
 * @method $this chFileATime(iFile $file, $time = null)
 * @method $this chFileMTime(iFile $file, $time = null)
 * @method $this unlink($file)
 * @method iPathFileUri pathUri()
 * @method iFsBase chDir(iDirectoryInfo $dir)
 */
class BaseWrapper implements iFilesystem
{
    /**
     * Wrapper Make Around Gear Filesystem
     *
     * @var iFsBase
     */
    protected $gear;

    /**
     * Construct
     *
     * @param iFilesystem $gear
     */
    function __construct(iFilesystem $gear)
    {
        $this->setGear($gear);
    }

    /**
     * Set Wrapper Gear
     *
     * ! Wrapper Make Around Gear Filesystem
     *
     * @param iFilesystem $filesystem
     *
     * @return $this
     */
    function setGear(iFilesystem $filesystem)
    {
        $this->gear = $filesystem;

        return $this;
    }

    /**
     * Get Wrapped Filesystem
     *
     * @return iFsBase
     */
    function gear()
    {
       return $this->gear;
    }

    /**
     * Proxy call to wrapped filesystem
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    function __call($method, $arguments)
    {
        if (method_exists($this->gear(), $method))
            return call_user_func_array([$this->gear(), $method], $arguments);

        return false;
    }

    /**
     * Get Property
     *
     * @param string $name
     *
     * @return mixed
     */
    function __get($name)
    {
        return $this->gear()->$name;
    }

    /**
     * Set Property
     *
     * @param string $name
     * @param mixed  $property
     *
     * @return mixed
     */
    function __set($name, $property)
    {
        return $this->gear()->$name = $property;
    }
}
