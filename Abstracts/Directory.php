<?php
namespace Poirot\Filesystem\Abstracts;

use Poirot\Filesystem\Interfaces\iDirectory;
use Poirot\Filesystem\Interfaces\iFilesystem;
use Poirot\Filesystem\Interfaces\iFilesystemAware;
use Poirot\Filesystem\Interfaces\iFilesystemProvider;

class Directory
    implements
    iDirectory,
    iFilesystemAware,
    iFilesystemProvider
{
    /**
     * Set Basename of file or folder
     *
     * ! throw exception if file is lock
     *
     * - /path/to/filename.ext
     * - /path/to/folderName/
     *
     * @param string $name Basename
     *
     * @return $this
     */
    function setBasename($name)
    {
        // TODO: Implement setBasename() method.
    }

    /**
     * Set Path
     *
     * ! throw exception if file is lock
     *
     * - if null storage use default/current path
     *
     * @param string|null $path Path To File/Folder
     *
     * @return $this
     */
    function setPath($path)
    {
        // TODO: Implement setPath() method.
    }

    /**
     * Rename File And Write To Storage
     *
     * @param string $newname New name
     *
     * @return $this
     */
    function rename($newname)
    {
        // TODO: Implement rename() method.
    }

    /**
     * Set Owner
     *
     * @param int $owner
     *
     * @return $this
     */
    function setOwner($owner)
    {
        // TODO: Implement setOwner() method.
    }

    /**
     * Set Permissions
     *
     * @param $perms
     *
     * @return $this
     */
    function setPerms($perms)
    {
        // TODO: Implement setPerms() method.
    }

    /**
     * Set Group
     *
     * @param $group
     *
     * @return $this
     */
    function setGroup($group)
    {
        // TODO: Implement setGroup() method.
    }

    /**
     * Tells if the entry is writable
     *
     * - The writable beside of filesystem must
     *   implement iWritable
     *
     * @return bool
     */
    function isWritable()
    {
        // TODO: Implement isWritable() method.
    }

    /**
     * Is File/Folder Exists?
     *
     * @return bool
     */
    function isExists()
    {
        // TODO: Implement isExists() method.
    }

    /**
     * Gets last access time of the file
     *
     * @return mixed
     */
    function getATime()
    {
        // TODO: Implement getATime() method.
    }

    /**
     * Gets the base name of the file
     *
     * - Include extension on files
     *
     * @return string
     */
    function getBasename()
    {
        // TODO: Implement getBasename() method.
    }

    /**
     * Gets the path without filename
     *
     * @return string
     */
    function getPath()
    {
        // TODO: Implement getPath() method.
    }

    /**
     * Get Path Name To File Or Folder
     *
     * - include full path for remote files
     *
     * @return string
     */
    function getRealPathName()
    {
        // TODO: Implement getRealPathName() method.
    }

    /**
     * Returns the inode change time for the file
     *
     * @return string Unix-TimeStamp
     */
    function getCTime()
    {
        // TODO: Implement getCTime() method.
    }

    /**
     * Gets the file group
     *
     * @return mixed
     */
    function getGroup()
    {
        // TODO: Implement getGroup() method.
    }

    /**
     * Gets the last modified time
     *
     * @return string Unix-TimeStamp
     */
    function getMTime()
    {
        // TODO: Implement getMTime() method.
    }

    /**
     * Gets the owner of the file
     *
     * @return mixed
     */
    function getOwner()
    {
        // TODO: Implement getOwner() method.
    }

    /**
     * Gets file permissions
     * Should return an or combination of the PERMISSIONS
     *
     * exp. from storage WRITABLE|EXECUTABLE
     *
     * @return mixed
     */
    function getPerms()
    {
        // TODO: Implement getPerms() method.
    }

    /**
     * Returns parent directory's path
     *
     * /etc/passwd => /etc
     *
     * @return string
     */
    function getDirname()
    {
        // TODO: Implement getDirname() method.
    }

    /**
     * get the mimetype for a file or folder
     * The mimetype for a folder is required to be "httpd/unix-directory"
     *
     * @return string
     */
    function getMimeType()
    {
        // TODO: Implement getMimeType() method.
    }

    /**
     * Tells if file is readable
     *
     * @return bool
     */
    function isReadable()
    {
        // TODO: Implement isReadable() method.
    }

    /**
     * Delete a directory from storage
     *
     * @return bool
     */
    function rmDir()
    {
        // TODO: Implement rmDir() method.
    }

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
    function copy(iDirectory $directory)
    {
        // TODO: Implement copy() method.
    }

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
    function move(iDirectory $directory)
    {
        // TODO: Implement move() method.
    }

    /**
     * List an array of files/directories Object from the directory
     *
     * @return array
     */
    function scanDir()
    {
        // TODO: Implement scanDir() method.
    }

    /**
     * Set Filesystem
     *
     * @param iFilesystem $filesystem
     *
     * @return $this
     */
    function setFilesystem(iFilesystem $filesystem)
    {
        // TODO: Implement setFilesystem() method.
    }

    /**
     * @return iFilesystem
     */
    function Filesystem()
    {
        // TODO: Implement Filesystem() method.
    }

    /**
     * Make File/Folder if not exists
     *
     * @return bool
     */
    function mkIfNotExists()
    {
        // TODO: Implement mkIfNotExists() method.
    }
}
 