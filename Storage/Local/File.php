<?php
namespace Poirot\Filesystem\Storage\Local;

use Poirot\Filesystem\Interfaces\iFile;

class File implements iFile
{
    /**
     * Set the file extension
     *
     * @param string|null $ext File Extension
     *
     * @return $this
     */
    function setExtension($ext)
    {
        // TODO: Implement setExtension() method.
    }

    /**
     * Reads entire file into a string
     *
     * @return string
     */
    function getContents()
    {
        // TODO: Implement getContents() method.
    }

    /**
     * Put Contents To File
     *
     * @param string $content Content
     *
     * @return $this
     */
    function putContents($content)
    {
        // TODO: Implement putContents() method.
    }

    /**
     * Rename File
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
     * Copy to new file
     *
     * @param iFile $file
     *
     * @return $this
     */
    function copy(iFile $file)
    {
        // TODO: Implement copy() method.
    }

    /**
     * Deletes a file
     *
     * @return bool
     */
    function unlink()
    {
        // TODO: Implement unlink() method.
    }

    /**
     * Gets the file extension
     *
     * @return string
     */
    function getExtension()
    {
        // TODO: Implement getExtension() method.
    }

    /**
     * Set Basename of file or folder
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
     * Make File/Folder if not exists
     *
     * @return bool
     */
    function mkIfNotExists()
    {
        // TODO: Implement mkIfNotExists() method.
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
     * exp. WRITABLE|EXECUTABLE
     *
     * @return mixed
     */
    function getPerms()
    {
        // TODO: Implement getPerms() method.
    }

    /**
     * Gets absolute path to file
     *
     * @return string
     */
    function getRealPath()
    {
        // TODO: Implement getRealPath() method.
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
     * Gets the filesize in bytes for the file referenced
     *
     * @return int
     */
    function getSize()
    {
        // TODO: Implement getSize() method.
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
     * Tells if the entry is writable
     *
     * @return bool
     */
    function isWritable()
    {
        // TODO: Implement isWritable() method.
    }

    /**
     * Set File Contents
     *
     * @param string $contents Contents
     *
     * @return $this
     */
    function setContents($contents)
    {
        // TODO: Implement setContents() method.
    }
}
 