<?php
namespace Poirot\Filesystem\Adapter\InMemory;

use Poirot\Filesystem\Adapter\Directory;
use Poirot\Filesystem\Exception\FileNotFoundException;
use Poirot\Filesystem\Interfaces\Filesystem\iCommon;
use Poirot\Filesystem\Interfaces\Filesystem\iCommonInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iDirectory;
use Poirot\Filesystem\Interfaces\Filesystem\iDirectoryInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iFile;
use Poirot\Filesystem\Interfaces\Filesystem\iFileInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iLinkInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iFilePermissions;
use Poirot\Filesystem\Interfaces\iFsBase;
use Poirot\PathUri\Interfaces\iPathFileUri;
use Poirot\PathUri\PathFileUri;
use Poirot\PathUri\PathJoinUri;

class InMemoryFS implements iFsBase
{
    protected $cwdPath = '/';

    protected $tree = [
        /*
        'nameOfDirectory' => [
            '__meta__' => [
                'type' => 'dir',
            ]
        ]
        */
    ];

    protected $__cachedSeekResolve = [];

    /**
     * Gets the current working directory
     *
     * - current working directory must exist
     *   from within root directory
     *
     * - if not chDir to root dir
     *
     * @throws \Exception On iFailure
     * @return iDirectory
     */
    function getCwd()
    {
        $return = new Directory($this->cwdPath);
        $return->setFilesystem($this);

        return $return;
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
        $dirPath = $dir->pathUri()
            ->fromPathUri($dir->pathUri());

        $seek = &$this->__seekTreeFromPath($dir->pathUri(), true);
        if (!$this->__fs_is_dir($seek))
            throw new \Exception(sprintf(
                'Failed Changing Directory To "%s".'
                , $dirPath->toString()
            ));

        $this->cwdPath = $dirPath->toString();

        return $this;
    }

    /**
     * Make an Object From Existence Path Filesystem
     *
     * @param string $path Filesystem Path To File or Directory
     *
     * @throws \Exception On Failure
     * @return iCommonInfo
     */
    function mkFromPath($path)
    {
        // TODO Implement Feature
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
     * @throws \Exception
     * @return iPathFileUri
     */
    function pathUri()
    {
        $pathFileUri = new PathFileUri;
        $pathFileUri->setSeparator(DIRECTORY_SEPARATOR);

        return $pathFileUri;
    }

    // Directory Implementation:

    /**
     * List an array of files/directories path from the directory
     *
     * - get rid of ".", ".." from list
     *
     * @param iDirectoryInfo|null $dir          If Null Scan Current Working Directory
     * @param int                 $sortingOrder SCANDIR_SORT_NONE|SCANDIR_SORT_ASCENDING
     *                                         |SCANDIR_SORT_DESCENDING
     *
     * @throws \Exception On Failure
     * @return array
     */
    function scanDir(iDirectoryInfo $dir = null, $sortingOrder = self::SCANDIR_SORT_NONE)
    {
        // TODO Implement Feature
    }

    // File Implementation:

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
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
    }

    /**
     * Checks whether a file or directory exists
     *
     * ! return FALSE for symlinks pointing to non-existing files
     *
     * @param iCommonInfo $cnode
     *
     * @return boolean
     */
    function isExists(iCommonInfo $cnode)
    {
        // TODO Implement Feature
    }

    /**
     * Write a string to a file
     *
     * - If filename does not exist, the file is created
     *
     * ! fails if you try to put a file in a directory that doesn't exist.
     *
     * @param iFile  $file
     * @param string $contents
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function putFileContents(iFile $file, $contents)
    {
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
    }

    /**
     * TODO Not Working
     *
     * Portable advisory file locking
     *
     * ! shared lock    (reader)
     *   exclusive lock (writer)
     *   release lock   (shared|exclusive)
     *
     * @param iFileInfo $file
     * @param int       $lock  LOCK_SH|LOCK_EX|LOCK_UN
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function flock(iFileInfo $file, $lock = LOCK_EX)
    {
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
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
     * @param string      $newName
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function rename(iCommonInfo $file, $newName)
    {
        // TODO Implement Feature
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
        // TODO Implement Feature
    }

    /**
     * Sets access time of file
     *
     * @param iFile $file
     * @param null  $time
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function chFileATime(iFile $file, $time = null)
    {
        // TODO Implement Feature
    }

    /**
     * Sets modification time of file
     *
     * @param iFile $file
     * @param null  $time
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function chFileMTime(iFile $file, $time = null)
    {
        // TODO Implement Feature
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
        // TODO Implement Feature
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
        // TODO Implement Feature
    }

    /**
     * Seek To A Tree From Path
     *
     * note: remember to call method with reference
     *       &$this->__seekTreeFromPath
     *       so we can have aliases tree result that
     *       point to updated latest or cached tree
     *       file structure
     *
     *       $this->tree['nameOfDirectory']['newFile'] = ['__meta__' => ['type' => 'file']];
     *       you always see latest data, because of reference
     *
     * @param iPathFileUri $path Path To File Or Dir
     * @param bool $throw Throw Exception
     *
     * @throws FileNotFoundException
     * @return array|bool
     */
    protected function &__seekTreeFromPath(iPathFileUri $path, $throw = false)
    {
        $path = clone $path;

        // Cached:
        $hash = md5($path->normalize()->toString());
        if (array_key_exists($hash, $this->__cachedSeekResolve)) {
            k('From Cache');
            return $this->__cachedSeekResolve[$hash];
        }


        // Initialize:

        $pathUri = new PathJoinUri([
            'path'      => $path->normalize()->toString(),
            'separator' => $this->pathUri()->getSeparator()
        ]);

        if (!$pathUri->isAbsolute())
            // Append current working directory
            $pathUri->prepend(new PathJoinUri([
                'path'      => $this->getCwd()->pathUri()->toString(),
                'separator' => $this->pathUri()->getSeparator()
            ]));

        $paths = $pathUri->getPath();

        // shift off home or absolute from array ['', ]
        // cause all addresses is absolute and have empty sign
        array_shift($paths);

        // Seek to tree:
        $seek = &$this->tree;
        while ($curr = array_shift($paths)) {
            if (!array_key_exists($curr, $seek)) {
                $seek = false;
                break;
            }

            $seek = &$this->tree[$curr];
        }

        // check throw:
        if ($seek === false && $throw)
            throw new FileNotFoundException(sprintf(
                'File "%s" Not Found.'
                , $this->pathUri()->fromPathUri($path)->toString()
            ));

        if ($seek !== false)
            $this->__cachedSeekResolve[$hash] = &$seek;

        return $seek;
    }

    /**
     * Check that given node is dir?
     *
     * @param array $node Tree Node
     *
     * @return bool
     */
    protected function __fs_is_dir(array $node)
    {
        $return = false;

        if (isset($node['__meta__']) && isset($node['__meta__']['type']))
            $return = ( $node['__meta__']['type'] == 'dir' );

        return $return;
    }
}
