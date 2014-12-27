<?php
namespace Poirot\Filesystem\Interfaces;

interface iDirectory extends iCommon, iDirectoryInfo, iStorage, iWritable
{
    /**
     * Delete a directory from storage
     *
     * @return bool
     */
    function rmDir();

    /**
     * Copy to new directory
     *
     * - Merge if directory exists
     * - Create If Directory Not Exists
     *
     * @param iDirectory $directory
     *
     * @return $this
     */
    function copy(iDirectory $directory);

    /**
     * Move to new directory
     *
     * ! use class copy/rmDir
     *
     * - Merge if directory exists
     * - Create If Directory Not Exists
     *
     * @param iDirectory $directory
     *
     * @return $this
     */
    function move(iDirectory $directory);

    /**
     * List an array of files and directories from the directory
     *
     * @return array
     */
    function scanDir();
}
