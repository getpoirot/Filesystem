<?php
namespace Poirot\Filesystem\Adapter\Virtual;

use Poirot\Filesystem\Adapter\Directory;
use Poirot\Filesystem\Exception\FileNotFoundException;
use Poirot\Filesystem\Interfaces\Filesystem\iCommon;
use Poirot\Filesystem\Interfaces\Filesystem\iCommonInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iDirectory;
use Poirot\Filesystem\Interfaces\Filesystem\iDirectoryInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iFile;
use Poirot\Filesystem\Interfaces\Filesystem\iFileInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iFilePermissions;
use Poirot\Filesystem\Interfaces\Filesystem\iLinkInfo;
use Poirot\Filesystem\Interfaces\iFilesystem;
use Poirot\Filesystem\Interfaces\iStorage;
use Poirot\PathUri\Interfaces\iPathFileUri;
use Poirot\PathUri\PathFileUri;

class VirtualFS implements iFilesystem
{
    protected $cwd = null;

    /**
     * @var iStorage
     */
    protected $storage;

    /**
     * Construct
     *
     * @param iStorage $storage
     */
    function __construct(/*iStorage*/ $storage)
    {
        $this->setStorage($storage);
    }

    /**
     * Set Storage
     *
     * @param iStorage $storage
     *
     * @return $this
     */
    function setStorage(/*iStorage*/ $storage)
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * Get Storage
     *
     * @return iStorage
     */
    function getStorage()
    {
        return $this->storage;
    }

    /**
     * Gets the current working directory
     *
     * - filesystem cwd result must get back
     *   from class pathUri()
     *
     * @throws \Exception On Failure
     * @return iDirectory
     */
    function getCwd()
    {
        if (!$this->cwd)
            $this->cwd = (new Directory($this->cwd));

        $this->cwd->setFilesystem($this);

        return $this->cwd;
    }

    /**
     * Make an Object From Existence Path Filesystem
     *
     * - Inject Current Filesystem into FSNode Object
     *   that makes on this method before return
     *
     * - All String Path created from this class can
     *   be passed as is
     *
     * @param string $path
     *
     * @throws \Exception On Failure
     * @return iCommonInfo
     */
    function mkFromPath($path)
    {
        // TODO: Implement mkFromPath() method.
    }

    /**
     * Get Path Uri Object
     *
     * - it used to build/parse uri address to file
     *   by filesystem
     *
     * - every time return clean/reset or new instance of
     *   pathUri
     *
     * @return iPathFileUri
     */
    function pathUri()
    {
        $pathFileUri = new PathFileUri;
        $pathFileUri->setSeparator(self::DS);

        return $pathFileUri;
    }

    /**
     * Changes Filesystem current directory
     *
     * @param iDirectoryInfo $dir
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function chDir(iDirectoryInfo $dir)
    {
        if ($this->isExists($dir))
            $this->cwd = $dir;
        else
            throw new FileNotFoundException(sprintf(
                'Directory "%s" not found in path: %s'
                , $dir->pathUri()->toString()
                , $this->getCwd()->pathUri()->toString()
            ));

        return $this;
    }

    /**
     * List an array of files/directories path from the directory
     *
     * - get rid of ".", ".." from list
     * - get relative path to current working directory
     *
     * @param iDirectoryInfo|null $dir If Null Scan Current Working Directory
     * @param int $sortingOrder SCANDIR_SORT_NONE|SCANDIR_SORT_ASCENDING
     *                                         |SCANDIR_SORT_DESCENDING
     *
     * @throws \Exception On Failure
     * @return array
     */
    function scanDir(iDirectoryInfo $dir = null, $sortingOrder = self::SCANDIR_SORT_NONE)
    {
        // TODO: Implement scanDir() method.
    }

    /**
     * Changes file group
     *
     * @param iCommonInfo $file Path to the file
     * @param mixed $group A group name or number
     *
     * @return $this
     */
    function chgrp(iCommonInfo $file, $group)
    {
        // TODO: Implement chgrp() method.
    }

    /**
     * Gets file group
     *
     * - Returns the group of the file
     *
     * @param iCommonInfo $node File Or Directory
     *
     * @throws \Exception On Failure
     * @return int|string
     */
    function getFileGroup(iCommonInfo $node)
    {
        // TODO: Implement getFileGroup() method.
    }

    /**
     * Changes file mode
     *
     * @param iCommonInfo $file Path to the file
     * @param iFilePermissions $mode
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function chmod(iCommonInfo $file, iFilePermissions $mode)
    {
        // TODO: Implement chmod() method.
    }

    /**
     * Gets file permissions
     *
     * @param iCommonInfo $file
     *
     * @return iFilePermissions
     */
    function getFilePerms(iCommonInfo $file)
    {
        // TODO: Implement getFilePerms() method.
    }

    /**
     * Changes file owner
     *
     * @param iCommonInfo $file Path to the file
     * @param string $user A user name or number
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function chown(iCommonInfo $file, $user)
    {
        // TODO: Implement chown() method.
    }

    /**
     * Gets file owner
     *
     * @param iCommonInfo $file
     *
     * @throws \Exception On Failure
     * @return int|string The user Name/ID of the owner of the file
     */
    function getFileOwner(iCommonInfo $file)
    {
        // TODO: Implement getFileOwner() method.
    }

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
    function copy(iCommonInfo $source, iCommon $dest)
    {
        // TODO: Implement copy() method.
    }

    /**
     * Is File?
     *
     * ! It's not necessary to check file existence on storage
     *   Just Perform Object Check
     *   It can be used with isExists() combination
     *
     * @param iCommon|string $source
     *
     * @return bool
     */
    function isFile($source)
    {
        // TODO: Implement isFile() method.
    }

    /**
     * Is Dir?
     *
     * ! It's not necessary to check file existence on storage
     *   Just Perform Object Check
     *   It can be used with isExists() combination
     *
     * @param iCommon|string $source
     *
     * @return bool
     */
    function isDir($source)
    {
        // TODO: Implement isDir() method.
    }

    /**
     * Is Link?
     *
     * ! It's not necessary to check file existence on storage
     *   Just Perform Object Check
     *   It can be used with isExists() combination
     *
     * @param iCommon|string $source
     *
     * @return bool
     */
    function isLink($source)
    {
        // TODO: Implement isLink() method.
    }

    /**
     * Returns available space on filesystem or disk partition
     *
     * - Returns the number of available bytes as a float
     * - Using Current Working Directory
     *
     * @return float|self::DISKSPACE_*
     */
    function getFreeSpace()
    {
        return self::DISKSPACE_UNKNOWN;
    }

    /**
     * Returns the total size of a filesystem or disk partition
     *
     * - Returns the number of available bytes as a float
     * - Using Current Working Directory
     *
     * @return float|self::DISKSPACE_*
     */
    function getTotalSpace()
    {
        return self::DISKSPACE_UNKNOWN;
    }

    /**
     * Checks whether a file or directory exists
     *
     * return FALSE for symlinks pointing to non-existing files
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
     * @throws \Exception On Failure
     * @return string
     */
    function getFileContents(iFile $file, $maxlen = 0)
    {
        // TODO: Implement getFileContents() method.
    }

    /**
     * Write a string to a file
     *
     * - If filename does not exist, the file is created
     *
     * @param iFile $file
     * @param string $contents
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function putFileContents(iFile $file, $contents)
    {
        // TODO: Implement putFileContents() method.
    }

    /**
     * Gets last access time of file
     *
     * @param iFileInfo $file
     *
     * @throws \Exception On Failure
     * @return int timestamp Unix timestamp
     */
    function getFileATime(iFileInfo $file)
    {
        // TODO: Implement getFileATime() method.
    }

    /**
     * Gets inode change time of file
     *
     * ! when the permissions, owner, group, or other
     *   metadata from the inode is updated
     *
     * @param iFileInfo $file
     *
     * @throws \Exception On Failure
     * @return int timestamp Unix timestamp
     */
    function getFileCTime(iFileInfo $file)
    {
        // TODO: Implement getFileCTime() method.
    }

    /**
     * Gets file modification time
     *
     * ! the time when the content of the file was changed
     *
     * @param iFileInfo $file
     *
     * @throws \Exception On Failure
     * @return int timestamp Unix timestamp
     */
    function getFileMTime(iFileInfo $file)
    {
        // TODO: Implement getFileMTime() method.
    }

    /**
     * Gets file size
     *
     * @param iFileInfo $file
     *
     * @throws \Exception On Failure
     * @return int In bytes
     */
    function getFileSize(iFileInfo $file)
    {
        // TODO: Implement getFileSize() method.
    }

    /**
     * Portable advisory file locking
     *
     * ! shared lock    (reader)
     *   exclusive lock (writer)
     *   release lock   (shared|exclusive)
     *
     * @param iFileInfo $file
     * @param int $lock LOCK_SH|LOCK_EX|LOCK_UN
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function flock(iFileInfo $file, $lock = LOCK_EX)
    {
        // TODO: Implement flock() method.
    }

    /**
     * Tells whether a file/directory exists and is readable
     *
     * ! checks whether you can do getFileContents() or similar calls
     *   for directories to fetch contents list
     *
     * @param iCommonInfo $file
     *
     * @return bool
     */
    function isReadable(iCommonInfo $file)
    {
        // TODO: Implement isReadable() method.
    }

    /**
     * Tells whether the file/directory is writable
     *
     * @param iCommonInfo $file
     *
     * @return bool TRUE if the filename exists and is writable
     */
    function isWritable(iCommonInfo $file)
    {
        // TODO: Implement isWritable() method.
    }

    /**
     * Create a hard link
     *
     * @param iLinkInfo $link
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function mkLink(iLinkInfo $link)
    {
        // TODO: Implement mkLink() method.
    }

    /**
     * Makes directory Recursively
     *
     * @param iDirectoryInfo $dir
     * @param iFilePermissions $mode
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function mkDir(iDirectoryInfo $dir, iFilePermissions $mode)
    {
        // TODO: Implement mkDir() method.
    }

    /**
     * Get Parent Directory Of Given File/Dir
     *
     * ! If there are no slashes in path, a current dir returned
     *
     * @param iCommonInfo $file
     *
     * @return iDirectory
     */
    function dirUp(iCommonInfo $file)
    {
        // TODO: Implement dirUp() method.
    }

    /**
     * Returns the base filename of the given path.
     *
     * @param iCommonInfo $file
     *
     * @return string
     */
    function getFilename(iCommonInfo $file)
    {
        // TODO: Implement getFilename() method.
    }

    /**
     * Get Extension Of File
     *
     * ! empty screen if dose`nt have ext
     *
     * @param iFileInfo $file
     *
     * @return string
     */
    function getFileExtension(iFileInfo $file)
    {
        // TODO: Implement getFileExtension() method.
    }

    /**
     * Get File/Folder Name Without Extension
     *
     * @param iCommonInfo $file
     *
     * @return string
     */
    function getBasename(iCommonInfo $file)
    {
        // TODO: Implement getBasename() method.
    }

    /**
     * Rename File Or Directory
     *
     * - new name can contains absolute path
     *   /new/path/to/renamed.file
     * - if new name is just name
     *   append file directory path to new name
     * - moving it between directories if necessary
     * - If newname exists, it will be overwritten
     *
     * @param iCommonInfo $file
     * @param string $newName
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function rename(iCommonInfo $file, $newName)
    {
        // TODO: Implement rename() method.
    }

    /**
     * Attempts to remove the directory
     *
     * - If Directory was not empty, attempt recursive
     *   remove for files and nested directories
     *
     * @param iDirectoryInfo $dir
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function rmDir(iDirectoryInfo $dir)
    {
        // TODO: Implement rmDir() method.
    }

    /**
     * Sets access time of file
     *
     * @param iFile $file
     * @param null $time
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function chFileATime(iFile $file, $time = null)
    {
        // TODO: Implement chFileATime() method.
    }

    /**
     * Sets modification time of file
     *
     * @param iFile $file
     * @param null $time
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function chFileMTime(iFile $file, $time = null)
    {
        // TODO: Implement chFileMTime() method.
    }

    /**
     * Returns the target of a symbolic link
     *
     * @param iLinkInfo $link
     *
     * @throws \Exception On Failure
     * @return iCommonInfo File or Directory
     */
    function linkRead(iLinkInfo $link)
    {
        // TODO: Implement linkRead() method.
    }

    /**
     * Deletes a file
     *
     * @param iFileInfo|iLinkInfo $file
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function unlink($file)
    {
        // TODO: Implement unlink() method.
    }
}
 