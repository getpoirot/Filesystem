<?php
namespace Poirot\Filesystem\Interfaces;

use Poirot\Filesystem\Interfaces\Filesystem\iPermissions;

interface iFilesystem
{
    const DS = DIRECTORY_SEPARATOR; // prefer to use '/' as a portable separator

    /**
     * Changes file group
     *
     * @param iCommonInfo $file Path to the file
     * @param mixed $group A group name or number
     *
     * @return $this
     */
    function chgrp(iCommonInfo $file, $group);

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
    function getFileGroup(iCommonInfo $file);

    /**
     * Changes file mode
     *
     * @param iCommonInfo $file Path to the file
     * @param iPermissions $mode
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function chmod(iCommonInfo $file, iPermissions $mode);

    /**
     * Gets file permissions
     *
     * @param iCommonInfo $file
     *
     * @return iPermissions
     */
    function getFilePerms(iCommonInfo $file);

    /**
     * Changes file owner
     *
     * @param iCommon $file Path to the file
     * @param string  $user A user name or number
     *
     * @return $this
     */
    function chown(iCommon $file, $user);

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
    function copy(iCommon $source, iCommon $dest);

    /**
     * Is File?
     *
     * @param iCommon $source
     *
     * @return bool
     */
    function isFile(iCommon $source);

    /**
     * Is Dir?
     *
     * @param iCommon $source
     *
     * @return bool
     */
    function isDir(iCommon $source);

    /**
     * Is Link?
     *
     * @param iCommon $source
     *
     * @return bool
     */
    function isLink(iCommon $source);

    /**
     * Returns available space on filesystem or disk partition
     *
     * - Returns the number of available bytes as a float
     *
     * @return float
     */
    function getFreeSpace();

    /**
     * Returns the total size of a filesystem or disk partition
     *
     * - Returns the number of available bytes as a float
     *
     * @return float
     */
    function getTotalSpace();

    /**
     * Checks whether a file or directory exists
     *
     * @param iCommonInfo $file
     *
     * @return boolean
     */
    function isExists(iCommonInfo $file);

    /**
     * Reads entire file into a string
     *
     * @param iFile $file
     * @param int   $maxlen Maximum length of data read
     *
     * @return string
     */
    function getFileContents(iFile $file, $maxlen = 0);

    /**
     * Write a string to a file
     *
     * @param iFile  $file
     * @param string $contents
     *
     * @return $this
     */
    function putFileContents(iFile $file, $contents);

    /**
     * Gets last access time of file
     *
     * @param iCommonInfo $file
     *
     * @return int timestamp
     */
    function getFileATime(iCommonInfo $file);

    /**
     * Gets inode change time of file
     *
     * @param iCommonInfo $file
     *
     * @return int timestamp
     */
    function getFileCTime(iCommonInfo $file);

    /**
     * Gets file modification time
     *
     * @param iCommonInfo $file
     *
     * @return int timestamp
     */
    function getFileMTime(iCommonInfo $file);

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
    function getFileOwner(iCommonInfo $file);

    /**
     * Gets file size
     *
     * @param iFile $file
     *
     * @return int In bytes
     */
    function getFileSize(iFile $file);

    /**
     * Portable advisory file locking
     *
     * @param iCommonInfo $file
     *
     * @return mixed
     */
    function flock(iCommonInfo $file);

    /**
     * Tells whether a file exists and is readable
     *
     * @param iFile $file
     *
     * @return bool
     */
    function isReadable(iFile $file);

    /**
     * Tells whether the filename is writable
     *
     * @param iFile $file
     *
     * @return bool
     */
    function isWritable(iFile $file);

    /**
     * Create a hard link
     *
     * @param iLink $link
     */
    function mkLink(iLink $link);

    /**
     * Makes directory Recursively
     *
     * @param iDirectory $dir
     * @param int        $mode
     *
     * @return
     */
    function mkDir(iDirectory $dir, $mode = 0777);

    function getDirname(iCommonInfo $file);

    function getBasename(iCommonInfo $file);

    function getFileExtension(iFileInfo $file);

    function getFilename(iCommonInfo $file);

    function rename();

    function rmDir(iDirectory $dir);

    /**
     * Creates a temporary file
     *
     * @return iFile
     */
    function tmpfile();

    /**
     * Sets access and modification time of file
     *
     * @param iFile $file
     * @param null $time
     */
    function touch(iFile $file, $time = null);

    /**
     * Returns the target of a symbolic link
     *
     * @param iLink $link
     */
    function linkRead(iLink $link);

    /**
     * Deletes a file
     * 
     * @param iCommonInfo $file
     */
    function unlink(iCommonInfo $file);
}
 