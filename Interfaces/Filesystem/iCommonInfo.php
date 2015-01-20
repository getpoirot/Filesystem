<?php
namespace Poirot\Filesystem\Interfaces\Filesystem;

interface iCommonInfo
{
    /**
     * Gets the file name of the file
     *
     * - Without extension on files
     *
     * @return string
     */
    function getBasename();

    /**
     * Gets the path without filename
     *
     * - Get CWDir (Filesystem) If Path Not Set
     *
     * @return string
     */
    function getPath();

    /**
     * Get Path Name To File Or Folder
     *
     * - include full path for remote files
     * - include extension for files
     * - usually use Util::normalizePath on return
     *
     * @return string
     */
    function getRealPathName();

    /**
     * Gets the file group
     *
     * @return mixed
     */
    function getGroup();

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
