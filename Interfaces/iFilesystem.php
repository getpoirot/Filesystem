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
     * @param iCommonInfo $file Path to the file
     * @param string $user A user name or number
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function chown(iCommonInfo $file, $user);

    /**
     * Gets file owner
     *
     * @param iCommonInfo $file
     *
     * @throws \Exception On Failure
     * @return int|string The user Name/ID of the owner of the file
     */
    function getFileOwner(iCommonInfo $file);

    /**
     * Copies file
     *
     * - Source is Directory:
     *      the destination must be a directory
     *      goto a
     * - Source is File:
     *      the destination can be a directory or file
     *          directory:
     *             (a) if exists it will be merged
     *                 not exists it will be created
     *          file:
     *              if file exists it will be overwrite
     *              copy source to destination with new name
     *
     * @param iCommonInfo $source
     * @param iCommon $dest
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function copy(iCommonInfo $source, iCommon $dest);

    /**
     * Is File?
     *
     * ! It's not necessary to check file existence on storage
     *   Just Perform Object Check
     *   It can be used with isExists() combination
     *
     * @param iCommon $source
     *
     * @return bool
     */
    function isFile(iCommon $source);

    /**
     * Is Dir?
     *
     * ! It's not necessary to check file existence on storage
     *   Just Perform Object Check
     *   It can be used with isExists() combination
     *
     * @param iCommon $source
     *
     * @return bool
     */
    function isDir(iCommon $source);

    /**
     * Is Link?
     *
     * ! It's not necessary to check file existence on storage
     *   Just Perform Object Check
     *   It can be used with isExists() combination
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
     * @param iDirectoryInfo $dir
     * @param iPermissions $mode
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function mkDir(iDirectoryInfo $dir, iPermissions $mode);

    /**
     * Get Parent Directory Of Given File/Dir
     *
     * ! If there are no slashes in path, a current dir returned
     *
     * @param iCommonInfo $file
     *
     * @return iDirectory
     */
    function getDirname(iCommonInfo $file);

    /**
     * Returns the base name of the given path.
     *
     * @param iCommonInfo $file
     *
     * @return string
     */
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
 