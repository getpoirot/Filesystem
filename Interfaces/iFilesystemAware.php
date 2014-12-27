<?php
namespace Poirot\Filesystem\Interfaces;

/**
 * Storage that use filesystem wrapper must implement this interface
 * Filesystem functions are not called directly; they are proxy call through
 * Method Filesystem()
 *
 */
interface iFilesystemAware
{
    /**
     * Set Filesystem
     *
     * @param iFilesystem $filesystem
     *
     * @return $this
     */
    function setFilesystem(iFilesystem $filesystem);
}
