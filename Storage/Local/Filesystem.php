<?php
namespace Poirot\Filesystem\Storage\Local;

use Poirot\Filesystem\Interfaces\Filesystem\iPermissions;
use Poirot\Filesystem\Interfaces\iCommon;
use Poirot\Filesystem\Interfaces\iCommonInfo;
use Poirot\Filesystem\Interfaces\iDirectory;
use Poirot\Filesystem\Interfaces\iFile;
use Poirot\Filesystem\Interfaces\iFileInfo;
use Poirot\Filesystem\Interfaces\iFilesystem;
use Poirot\Filesystem\Interfaces\iLink;
use Poirot\Filesystem\Permissions;

class Filesystem implements iFilesystem
{
    /**
     * Changes file group
     *
     * @param iCommonInfo $file Path to the file
     * @param mixed $group A group name or number
     *
     * @throws \Exception
     * @return $this
     */
    function chgrp(iCommonInfo $file, $group)
    {
        $this->validateFile($file);

        $filename = $file->getRealPathName();
        if (!chgrp($filename, $group))
            throw new \Exception(sprintf(
                'Failed Changing Group Of "%s" File.'
            ), $filename);

        clearstatcache(); // don't cache result

        return $this;
    }

    /**
     * Gets file group
     *
     * - Returns the group of the file
     *
     * @param iCommonInfo $file
     *
     * @throws \Exception On Failure
     * @return int|string
     */
    function getFileGroup(iCommonInfo $file)
    {
        $this->validateFile($file);

        $filename = $file->getRealPathName();

        $group = @posix_getgrgid(filegroup($filename));
        if (!$group)
            throw new \Exception(sprintf(
                'Failed To Know Group Of "%s" File.'
            ), $filename);

        clearstatcache(); // don't cache result

        return $group;
    }

    /**
     * Changes file mode
     *
     * @param iCommonInfo $file Path to the file
     * @param iPermissions $mode
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function chmod(iCommonInfo $file, iPermissions $mode)
    {
        $this->validateFile($file);

        $filename = $file->getRealPathName();

        if (!chmod($filename, $mode->getTotalPerms()))
            throw new \Exception(sprintf(
                'Failed To Change Owner Of "%s" File.'
            ), $filename);

        clearstatcache(); // don't cache result

        return $this;
    }

    /**
     * Gets file permissions
     *
     * @param iCommonInfo $file
     *
     * @return iPermissions
     */
    function getFilePerms(iCommonInfo $file)
    {
        $this->validateFile($file);

        $fperm = @fileperms($file->getRealPathName());

        $perms = new Permissions();
        $perms->grantPermission($fperm);

        return $perms;
    }

    /**
     * Changes file owner
     *
     * @param iCommon $file Path to the file
     * @param string $user A user name or number
     *
     * @return $this
     */
    function chown(iCommon $file, $user)
    {
        // TODO: Implement chown() method.
    }

    /**
     * Copies file
     *
     * - If Destination Exists It Will Be Merged
     * - If Source Is Directory Can't Copied To File
     * - If Source Is File
     *   if dest. is file copy with new name
     *   else if is directory copy to directory with same name
     *
     * @param iCommon $source
     * @param iCommon $dest
     *
     * @return $this
     */
    function copy(iCommon $source, iCommon $dest)
    {
        // TODO: Implement copy() method.
    }

    /**
     * Is File?
     *
     * @param iCommon $source
     *
     * @return bool
     */
    function isFile(iCommon $source)
    {
        // TODO: Implement isFile() method.
    }

    /**
     * Is Dir?
     *
     * @param iCommon $source
     *
     * @return bool
     */
    function isDir(iCommon $source)
    {
        // TODO: Implement isDir() method.
    }

    /**
     * Is Link?
     *
     * @param iCommon $source
     *
     * @return bool
     */
    function isLink(iCommon $source)
    {
        // TODO: Implement isLink() method.
    }

    /**
     * Returns available space on filesystem or disk partition
     *
     * - Returns the number of available bytes as a float
     *
     * @return float
     */
    function getFreeSpace()
    {
        // TODO: Implement getFreeSpace() method.
    }

    /**
     * Returns the total size of a filesystem or disk partition
     *
     * - Returns the number of available bytes as a float
     *
     * @return float
     */
    function getTotalSpace()
    {
        // TODO: Implement getTotalSpace() method.
    }

    /**
     * Checks whether a file or directory exists
     *
     * @param iCommonInfo $file
     *
     * @return boolean
     */
    function isExists(iCommonInfo $file)
    {
        // TODO: Implement isExists() method.
    }

    /**
     * Reads entire file into a string
     *
     * @param iFile $file
     * @param int $maxlen Maximum length of data read
     *
     * @return string
     */
    function getFileContents(iFile $file, $maxlen = 0)
    {
        // TODO: Implement getFileContents() method.
    }

    /**
     * Write a string to a file
     *
     * @param iFile $file
     * @param string $contents
     *
     * @return $this
     */
    function putFileContents(iFile $file, $contents)
    {
        // TODO: Implement putFileContents() method.
    }

    /**
     * Gets last access time of file
     *
     * @param iCommonInfo $file
     *
     * @return int timestamp
     */
    function getFileATime(iCommonInfo $file)
    {
        // TODO: Implement getFileATime() method.
    }

    /**
     * Gets inode change time of file
     *
     * @param iCommonInfo $file
     *
     * @return int timestamp
     */
    function getFileCTime(iCommonInfo $file)
    {
        // TODO: Implement getFileCTime() method.
    }

    /**
     * Gets file modification time
     *
     * @param iCommonInfo $file
     *
     * @return int timestamp
     */
    function getFileMTime(iCommonInfo $file)
    {
        // TODO: Implement getFileMTime() method.
    }

    /**
     * Gets file owner
     *
     * - Returns the user ID of the owner of the file
     * ! The user ID is returned in numerical format,
     *   use posix_getpwuid() to resolve it to a username
     *
     * @param iCommonInfo $file
     *
     * @return int|string
     */
    function getFileOwner(iCommonInfo $file)
    {
        // TODO: Implement getFileOwner() method.
    }

    /**
     * Gets file size
     *
     * @param iFile $file
     *
     * @return int In bytes
     */
    function getFileSize(iFile $file)
    {
        // TODO: Implement getFileSize() method.
    }

    /**
     * Portable advisory file locking
     *
     * @param iCommonInfo $file
     *
     * @return mixed
     */
    function flock(iCommonInfo $file)
    {
        // TODO: Implement flock() method.
    }

    /**
     * Tells whether a file exists and is readable
     *
     * @param iFile $file
     *
     * @return bool
     */
    function isReadable(iFile $file)
    {
        // TODO: Implement isReadable() method.
    }

    /**
     * Tells whether the filename is writable
     *
     * @param iFile $file
     *
     * @return bool
     */
    function isWritable(iFile $file)
    {
        // TODO: Implement isWritable() method.
    }

    /**
     * Create a hard link
     *
     * @param iLink $link
     */
    function mkLink(iLink $link)
    {
        // TODO: Implement mkLink() method.
    }

    /**
     * Makes directory Recursively
     *
     * @param iDirectory $dir
     * @param int $mode
     *
     * @return
     */
    function mkDir(iDirectory $dir, $mode = 0777)
    {
        // TODO: Implement mkDir() method.
    }

    function getDirname(iCommonInfo $file)
    {
        // TODO: Implement getDirname() method.
    }

    function getBasename(iCommonInfo $file)
    {
        // TODO: Implement getBasename() method.
    }

    function getFileExtension(iFileInfo $file)
    {
        // TODO: Implement getFileExtension() method.
    }

    function getFilename(iCommonInfo $file)
    {
        // TODO: Implement getFilename() method.
    }

    function rename()
    {
        // TODO: Implement rename() method.
    }

    function rmDir(iDirectory $dir)
    {
        // TODO: Implement rmDir() method.
    }

    /**
     * Creates a temporary file
     *
     * @return iFile
     */
    function tmpfile()
    {
        // TODO: Implement tmpfile() method.
    }

    /**
     * Sets access and modification time of file
     *
     * @param iFile $file
     * @param null $time
     */
    function touch(iFile $file, $time = null)
    {
        // TODO: Implement touch() method.
    }

    /**
     * Returns the target of a symbolic link
     *
     * @param iLink $link
     */
    function linkRead(iLink $link)
    {
        // TODO: Implement linkRead() method.
    }

    /**
     * Deletes a file
     *
     * @param iCommonInfo $file
     */
    function unlink(iCommonInfo $file)
    {
        // TODO: Implement unlink() method.
    }

    /**
     * - is file/folder
     * - is exists
     *
     * @param iCommonInfo $file
     * @throws \Exception
     */
    protected function validateFile(iCommonInfo $file)
    {
        $filename = $file->getRealPathName();
        if (!$this->isFile($file) || $this->isDir($file))
            throw new \Exception(sprintf(
                'The Destination File "%s" Must Be a File Or Folder.'
            ), $filename);
        elseif (!$this->isExists($file))
            throw new \Exception(sprintf(
                'File "%s" Not Found.'
            ), $filename);
    }
}
 