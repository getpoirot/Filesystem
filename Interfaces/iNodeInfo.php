<?php
namespace Poirot\Filesystem;

interface iNodeInfo
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
     * exp. WRITABLE|EXECUTABLE
     *
     * @return mixed
     */
    function getPerms();

    /**
     * Gets absolute path to file
     *
     * @return string
     */
    function getRealPath();

    /**
     * get the mimetype for a file or folder
     * The mimetype for a folder is required to be "httpd/unix-directory"
     *
     * @return string
     */
     function getMimeType();

    /**
     * Gets the filesize in bytes for the file referenced
     *
     * @return int
     */
    function getSize();

    /**
     * Tells if file is readable
     *
     * @return bool
     */
    function isReadable();

    /**
     * Tells if the entry is writable
     *
     * @return bool
     */
    function isWritable();
}
