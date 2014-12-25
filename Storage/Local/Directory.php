<?php
namespace Poirot\Filesystem\Storage\Local;

use Poirot\Filesystem\Interfaces\iFile;
use Poirot\Filesystem\Interfaces\iFolder;
use Poirot\Filesystem\Interfaces\iLink;
use Poirot\Filesystem\Interfaces\iNode;
use Poirot\Filesystem\Storage\AbstractStorageIterate;
use Traversable;

class Directory extends AbstractStorageIterate
    implements iFolder
{
    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        // TODO: Implement getIterator() method.
    }

    /**
     * Delete a directory
     *
     * @return bool
     */
    function rmDir()
    {
        // TODO: Implement rmDir() method.
    }

    /**
     * Change Current Work Dir To Folder
     *
     * @return $this
     */
    function chToDir()
    {
        // TODO: Implement chToDir() method.
    }

    /**
     * List an array of files and directories from the directory
     *
     * @return array
     */
    function scanDir()
    {
        // TODO: Implement scanDir() method.
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
     * @return $this
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
     * Current Working Directory
     *
     * - storage with empty working directory
     *   mean the base storage
     * - with creating files or folder cwd will
     *   append as path
     *
     * @return string
     */
    function getCwd()
    {
        // TODO: Implement getCwd() method.
    }

    /**
     * List Contents
     *
     * @return array[iFile|iLink|iFolder]
     */
    function lsContent()
    {
        // TODO: Implement lsContent() method.
    }

    /**
     * Create new Folder Instance
     *
     * @return iFolder
     */
    function dir()
    {
        // TODO: Implement dir() method.
    }

    /**
     * Create new File Instance
     *
     * @return iFile
     */
    function file()
    {
        // TODO: Implement file() method.
    }

    /**
     * Create new Link Instance
     *
     * @return iLink
     */
    function link()
    {
        // TODO: Implement link() method.
    }

    /**
     * Open Existence File Or Folder
     *
     * @param iNode $node File/Folder
     *
     * @return iNode|iFile|iLink
     */
    function open(iNode $node)
    {
        // TODO: Implement open() method.
    }

    /**
     * Write File To Storage
     *
     * @param iNode|iFile|iFolder|iLink $node File
     *
     * @return $this
     */
    function write(iNode $node)
    {
        // TODO: Implement write() method.
    }

    /**
     * Create File Or Folder From Given Path
     *
     * - if not exists
     *   name without extension considered as folder
     *   else this is file
     * - if exists
     *   check type of current node and make object
     *
     * @param string $path Path
     *
     * @return mixed
     */
    function createFromPath($path)
    {
        // TODO: Implement createFromPath() method.
    }
}
 