<?php
namespace Poirot\Filesystem\Interfaces\Filesystem;

interface iFileInfo extends iCommonInfo
{
    /**
     * Get Filename Include File Extension
     *
     * ! It's a combination of basename+'.'.extension
     *   combined with a dot
     *
     * @return string
     */
    function getFilename();

    /**
     * Gets the file extension
     *
     * @return string
     */
    function getExtension();

    /**
     * Gets the file size in bytes for the file referenced
     *
     * @return int
     */
    function getSize();

    /**
     * Gets last access time of the file
     *
     * @return int Unix-TimeStamp
     */
    function getATime();

    /**
     * Returns the inode change time for the file
     *
     * @return int Unix-TimeStamp
     */
    function getCTime();

    /**
     * Gets the last modified time
     *
     * @return int Unix-TimeStamp
     */
    function getMTime();
}
