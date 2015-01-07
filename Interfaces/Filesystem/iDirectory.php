<?php
namespace Poirot\Filesystem\Interfaces;

interface iDirectory extends iDirectoryInfo, iCommon
{
    /**
     * Delete a directory from storage
     *
     * @return bool
     */
    function rmDir();

    /**
     * Makes directory Recursively
     *
     * @return $this
     */
    function mkDir();

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
     * - Use Temp Folder For Safe Move
     *
     * @param iDirectory $directory
     *
     * @return $this
     */
    function move(iDirectory $directory);

    /**
     * List an array of files/directories Object from the directory
     *
     * @return array
     */
    function scanDir();
}
