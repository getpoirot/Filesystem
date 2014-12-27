<?php
namespace Poirot\Filesystem\Interfaces;

interface iFilesystem
{
    const DS = DIRECTORY_SEPARATOR;

    /**
     * Changes file group
     *
     * @param iCommon $file  Path to the file
     * @param mixed   $group A group name or number
     *
     * @return $this
     */
    function chgrp(iCommon $file, $group);

    /**
     * Changes file mode
     *
     * @param iCommon $file Path to the file
     * @param int     $mode Note that mode is not automatically
     *                      assumed to be an octal value, so to
     *                      ensure the expected operation,
     *                      you need to prefix mode with a zero (0)
     *
     * @return $this
     */
    function chmod(iCommon $file, $mode);

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
     * Gets file group
     *
     * - Returns the group ID of the file
     * ! The group ID is returned in numerical format,
     *   use posix_getgrgid() to resolve it to a group name.
     *
     * @param iCommonInfo $file
     *
     * @return int|string
     */
    function getFileGroup(iCommonInfo $file);

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
     * Gets file permissions
     *
     * @param iCommonInfo $file
     *
     * @return int
     */
    function getFilePerms(iCommonInfo $file);

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
 