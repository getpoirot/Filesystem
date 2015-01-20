<?php
namespace Poirot\Filesystem\Abstracts;

use Poirot\Core\BuilderSetterTrait;
use Poirot\Filesystem\Interfaces\Filesystem\iDirectory;
use Poirot\Filesystem\Interfaces\Filesystem\iFile;
use Poirot\Filesystem\Interfaces\Filesystem\iPermissions;
use Poirot\Filesystem\Interfaces\iFilesystem;
use Poirot\Filesystem\Interfaces\iFilesystemAware;
use Poirot\Filesystem\Interfaces\iFilesystemProvider;
use Poirot\Filesystem\Local\Filesystem;
use Poirot\Filesystem\Permissions;
use Poirot\Filesystem\Util;

class File extends Common
    implements
    iFile
{
    /**
     * @var file content internal cache
     */
    protected $_fcontent;

    /**
     * Set Owner
     *
     * @param int $owner
     *
     * @return $this
     */
    function chown($owner)
    {
        $this->filesystem()->chown($this, $owner);

        return $this;
    }

    /**
     * Gets the owner of the file
     *
     * @return mixed
     */
    function getOwner()
    {
        return $this->filesystem()->getFileOwner($this);
    }

    /**
     * Changes file mode
     *
     * @param iPermissions $mode
     *
     * @return $this
     */
    function chmod(iPermissions $mode)
    {
        $this->filesystem()->chmod($this, $mode);

        return $this;
    }

    /**
     * Gets file permissions
     * Should return an or combination of the PERMISSIONS
     *
     * exp. from storage WRITABLE|EXECUTABLE
     *
     * @return iPermissions
     */
    function getPerms()
    {
        return $this->filesystem()->getFilePerms($this);
    }

    /**
     * Set Group
     *
     * @param $group
     *
     * @return $this
     */
    function chgrp($group)
    {
        $this->filesystem()->chgrp($this, $group);

        return $this;
    }

    /**
     * Gets the file group
     *
     * @return mixed
     */
    function getGroup()
    {
        return $this->filesystem()->getFileGroup($this);
    }

    /**
     * Returns parent directory's path
     *
     * /etc/passwd => /etc
     *
     * @return iDirectory
     */
    function dirUp()
    {
        return $this->filesystem()->dirUp($this);
    }

    /**
     * Copy to new directory
     *
     * - Merge if directory exists
     * - Create If Directory Not Exists
     *
     * @param $fileDir
     *
     * @return $this
     */
    function copy($fileDir)
    {
        $this->filesystem()->copy($this, $fileDir);

        return $this;
    }

    /**
     * Move to new directory
     *
     * ! use class copy/rmDir
     *
     * - Merge if directory exists
     * - Create If Directory Not Exists
     * - Use Temp Folder For Safe Move
     *
     * @param iDirectory $fileDir
     *
     * @return $this
     */
    function move($fileDir)
    {

    }

    /**
     * Is File/Folder Exists?
     *
     * @return bool
     */
    function isExists()
    {
        return $this->Filesystem()->isExists($this);
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
        return $this->filesystem()->isWritable($this);
    }

    /**
     * Tells if file is readable
     *
     * @return bool
     */
    function isReadable()
    {
        return $this->filesystem()->isReadable($this);
    }

    /**
     * Lock File
     *
     * @return $this
     */
    function lock()
    {
        $this->filesystem()->flock($this);

        return $this;
    }

    /**
     * Unlock file
     *
     * @return $this
     */
    function unlock()
    {
        $this->filesystem()->flock($this, LOCK_UN);

        return $this;
    }

    /**
     * Reads entire file into a string
     *
     * ! if file not exists return null
     * ! check permissions, getPerms
     *
     * @return string|null
     */
    function getContents()
    {
        if ($this->_fcontent === null)
            if ($this->isExists())
                $this->setContents(
                    $this->filesystem()->getFileContents($this)
                );

        return $this->_fcontent;
    }

    /**
     * Set File Contents
     *
     * ! check permissions, getPerms
     *
     * @param string $contents Contents
     *
     * @return $this
     */
    function setContents($contents)
    {
        $this->_fcontent = (string) $contents;

        return $this;
    }

    /**
     * Put File Contents to Storage
     *
     * - If Content provided, it must use set content method
     *   OtherWise Use Current Content With getContent method
     *
     * @param string|null $content Content
     *
     * @return $this
     */
    function putContents($content = null)
    {
        if ($content !== null)
            $this->setContents($content);
        else
            $content = $this->getContents();

        $this->filesystem()->putFileContents($this, $content);

        return $this;
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
        $this->filesystem()->rename($this, $newname);

        return $this;
    }

    /**
     * Deletes a file from storage
     *
     * @return $this
     */
    function unlink()
    {
        $this->filesystem()->unlink($this);

        return $this;
    }

    /**
     * Gets the file size in bytes for the file referenced
     *
     * @return int
     */
    function getSize()
    {
        return $this->filesystem()->getFileSize($this);
    }

    /**
     * Gets last access time of the file
     *
     * @return int Unix-TimeStamp
     */
    function getATime()
    {
        return $this->filesystem()->getFileATime($this);
    }

    /**
     * Returns the inode change time for the file
     *
     * @return int Unix-TimeStamp
     */
    function getCTime()
    {
        return $this->filesystem()->getFileCTime($this);
    }

    /**
     * Gets the last modified time
     *
     * @return int Unix-TimeStamp
     */
    function getMTime()
    {
        return $this->filesystem()->getFileMTime($this);
    }
}
