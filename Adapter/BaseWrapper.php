<?php
namespace Poirot\Filesystem\Adapter;

use Poirot\Filesystem\Interfaces\iFilesystem;

/**
 * @method string getFilename(iCommonInfo $file)
 * @method string getFileExtension(iFileInfo $file)
 * ...@inheritdoc{iFilesystem}
 */
class BaseWrapper
{
    /**
     * Wrapper Make Around Gear Filesystem
     *
     * @var iFilesystem
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
     * @return iFilesystem
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
 