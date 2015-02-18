<?php
namespace Poirot\Filesystem\Interfaces\Filesystem;

interface iCommonInfo
{
    /**
     * Get Path Uri Filename
     *
     * - it used to build uri address to file
     *
     * @return iFSPathUri
     */
    function filePath();

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
     * @return iFilePermissions
     */
    function getPerms();

    /**
     * Returns parent directory's path
     *
     * /etc/passwd => /etc
     *
     * @return iDirectory
     */
    function dirUp();

    /**
     * Tells if file is readable
     *
     * @return bool
     */
    function isReadable();
}
