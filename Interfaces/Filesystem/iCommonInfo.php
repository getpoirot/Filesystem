<?php
namespace Poirot\Filesystem\Interfaces;

use Poirot\Filesystem\Interfaces\Filesystem\iPermissions;

interface iCommonInfo
{
    /**
     * Gets last access time of the file
     *
     * @return mixed
     */
    function getATime();

    /**
     * Gets the base name of the file
     *
     * - Include extension on files
     *
     * @return string
     */
    function getBasename();

    /**
     * Gets the path without filename
     *
     * @return string
     */
    function getPath();

    /**
     * Get Path Name To File Or Folder
     *
     * - include full path for remote files
     * - include extension for files
     *
     * @return string
     */
    function getRealPathName();

    /**
     * Returns the inode change time for the file
     *
     * @return string Unix-TimeStamp
     */
    function getCTime();

    /**
     * Gets the file group
     *
     * @return mixed
     */
    function getGroup();

    /**
     * Gets the last modified time
     *
     * @return string Unix-TimeStamp
     */
    function getMTime();

    /**
     * Gets the owner of the file
     *
     * @return mixed
     */
    function getOwner();

    /**
     * Gets file permissions
     * Should return an or combination of the PERMISSIONS
     *
     * exp. from storage WRITABLE|EXECUTABLE
     *
     * @return iPermissions
     */
    function getPerms();

    /**
     * Returns parent directory's path
     *
     * /etc/passwd => /etc
     *
     * @return string
     */
    function getDirname();

    /**
     * Tells if file is readable
     *
     * @return bool
     */
    function isReadable();
}
