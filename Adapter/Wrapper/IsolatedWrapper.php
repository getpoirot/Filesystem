<?php
namespace Poirot\Filesystem\Adapter\Wrapper;

use Poirot\Filesystem\Adapter\BaseWrapper;
use Poirot\Filesystem\Adapter\Directory;
use Poirot\Filesystem\Interfaces\Filesystem\iCommon;
use Poirot\Filesystem\Interfaces\Filesystem\iCommonInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iDirectory;
use Poirot\Filesystem\Interfaces\Filesystem\iDirectoryInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iFile;
use Poirot\Filesystem\Interfaces\Filesystem\iFileInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iFilePermissions;
use Poirot\Filesystem\Interfaces\Filesystem\iLinkInfo;
use Poirot\Filesystem\Interfaces\iFilesystem;
use Poirot\Filesystem\Interfaces\iFsBase;
use Poirot\PathUri\Interfaces\iPathFileUri;
use Poirot\PathUri\Interfaces\iPathJoinedUri;
use Poirot\PathUri\PathJoinUri;

class IsolatedWrapper extends BaseWrapper
{
    /**
     * @var iPathJoinedUri
     */
    protected $rootDir;

    /**
     * cached real dir path on latest chDir
     * @var string
     */
    protected $__lastCDir;

    /**
     * Construct
     *
     * @param iFilesystem $filesystem
     * @param null|iPathJoinedUri|string $rootDir
     *
     * @throws \Exception
     */
    function __construct(iFilesystem $filesystem, $rootDir = null)
    {
        parent::__construct($filesystem);

        if ($rootDir !== null)
            $this->chRootPath($rootDir);
    }

    /**
     * Changes Root Directory Path
     *
     * - root directory must be absolute
     *
     * @param string|iPathJoinedUri $dir
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function chRootPath($dir)
    {
        if (!is_string($dir) && !$dir instanceof iPathJoinedUri)
            throw new \Exception(sprintf(
                'Dir Path must be string or instanceof iPathJoinedUri but "%s" given.'
                , is_object($dir) ? get_class($dir) : gettype($dir)
            ));

        if (is_string($dir))
            $dir = new PathJoinUri([
                'path'      => $dir,
                'separator' => $this->pathUri()->getSeparator()
            ]);

        $dir->setSeparator(
            $this->pathUri()->getSeparator()
        );

        $dir->normalize();

        if (!$dir->isAbsolute()
            || !is_dir($dir->toString())
        )
            throw new \Exception(sprintf(
                'Dir path must be an absolute address, to an existence directory.'
            ));

        // Set Root Dir:
        $this->rootDir = $dir;

        // Finalize:

        // Check that current working directory is within root path >>>>> {
        $rdPath = new PathJoinUri($dir->toString());
        $cdPath  = new PathJoinUri([
            'path'      => $this->gear()->getCwd()->pathUri()->toString(),
            #'separator' => $this->pathUri()->getSeparator()
        ]);

        // (root = /var/www/) mask (cwd = /var/www/html) === root
        $trdPath = clone $rdPath;
        if ($trdPath->joint($cdPath, false)->getPath() !== $rdPath->getPath()) {
            // current directory is not within root directory
            // change current directory to root
            $this->chDir(new Directory('/'));
        }
        // <<<<<< }

        return $this;
    }

    /**
     * Get Root Directory Path
     *
     * @return iPathJoinedUri
     */
    function getRootPath()
    {
        if (!$this->rootDir)
            // root "/"
            $this->chRootPath(new PathJoinUri(
                [ 'path' => [''] ]
            ));

        return $this->rootDir;
    }

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
        $dirObj = $this->gear()->getCwd();
        $cwd    = $dirObj->pathUri()->toString();

        $rdPath = new PathJoinUri($this->getRootPath()->toString());

        // check cwd scope:
        if ($this->__lastCDir !== null
            && $cwd !== $this->__lastCDir
        ) {
            // Current Directory Changed Outside of class scope
            $ldPath  = new PathJoinUri([
                'path'      => $this->__lastCDir,
                'separator' => $this->pathUri()->getSeparator()
            ]);
            $path = $ldPath->mask($rdPath)
                ->prepend(new PathJoinUri($this->pathUri()->getSeparator()))
                ->toString()
            ;

            // restore cwd:
            $this->chDir(new Directory($path));

            return $this->getCwd();
        }

        $cdPath  = new PathJoinUri([
            'path'      => $cwd,
            'separator' => $this->pathUri()->getSeparator()
        ]);

        // Make Paths Absolute From Root
        // if root is      [/var/www/data]
        // and real cwd is [/var/www/data/]images
        // we turn it into /images
        $path = $cdPath->mask($rdPath)
            ->prepend(new PathJoinUri($this->pathUri()->getSeparator()))
            ->toString()
        ;

        $return = new Directory($path);
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
        $dirRealpath = $this->__getRealPathFromIsolatedPath($dir);

        $this->__lastCDir = $dirRealpath;

        return $this->gear()->chDir(new Directory($dirRealpath));
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
        $realPath = $this->__getRealPathFromIsolatedPath($path);
        $nodeObj  = $this->gear()->mkFromPath($realPath);

        return $this->__getIsolatedPathNode($nodeObj);
    }

    /**
     * List an array of files/directories path from the directory
     *
     * - get rid of ".", ".." from list
     * - get relative path to current working directory
     *
     * @param iDirectoryInfo|null $dir          If Null Scan Current Working Directory
     * @param int                 $sortingOrder SCANDIR_SORT_NONE|SCANDIR_SORT_ASCENDING
     *                                         |SCANDIR_SORT_DESCENDING
     *
     * @throws \Exception On Failure
     * @return array
     */
    function scanDir(iDirectoryInfo $dir = null, $sortingOrder = iFsBase::SCANDIR_SORT_NONE)
    {
        $pathStr = $this->__getRealPathFromIsolatedPath($dir);
        $dir     = $this->__changeNodePathFromString($dir, $pathStr);

        return $this->gear()->scanDir($dir, $sortingOrder);
    }

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
        $pathStr = $this->__getRealPathFromIsolatedPath($file);
        $file    = $this->__changeNodePathFromString($file, $pathStr);

        return $this->gear()->chgrp($file, $group);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($file);
        $file    = $this->__changeNodePathFromString($file, $pathStr);

        return $this->gear()->getFileGroup($file);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($file);
        $file    = $this->__changeNodePathFromString($file, $pathStr);

        return $this->gear()->chmod($file, $mode);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($file);
        $file    = $this->__changeNodePathFromString($file, $pathStr);

        return $this->gear()->getFilePerms($file);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($file);
        $file    = $this->__changeNodePathFromString($file, $pathStr);

        return $this->gear()->chown($file, $user);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($file);
        $file    = $this->__changeNodePathFromString($file, $pathStr);

        return $this->gear()->getFileOwner($file);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($source);
        $source  = $this->__changeNodePathFromString($source, $pathStr);

        $pathStr = $this->__getRealPathFromIsolatedPath($dest);
        $dest    = $this->__changeNodePathFromString($dest, $pathStr);

        return $this->gear()->copy($source, $dest);
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
        if (is_string($source))
            $source = $this->__getRealPathFromIsolatedPath($source);

        return $this->gear()->isFile($source);
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
        if (is_string($source))
            $source = $this->__getRealPathFromIsolatedPath($source);

        return $this->gear()->isDir($source);
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
        if (is_string($source))
            $source = $this->__getRealPathFromIsolatedPath($source);

        return $this->gear()->isLink($source);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($cnode);
        $file    = $this->__changeNodePathFromString($cnode, $pathStr);

        return $this->gear()->isExists($file);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($file);
        $file    = $this->__changeNodePathFromString($file, $pathStr);

        return $this->gear()->putFileContents($file, $contents);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($file);
        $file    = $this->__changeNodePathFromString($file, $pathStr);

        return $this->gear()->getFileContents($file, $maxlen);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($file);
        $file    = $this->__changeNodePathFromString($file, $pathStr);

        return $this->gear()->getFileATime($file);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($file);
        $file    = $this->__changeNodePathFromString($file, $pathStr);

        return $this->gear()->getFileCTime($file);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($file);
        $file    = $this->__changeNodePathFromString($file, $pathStr);

        return $this->gear()->getFileMTime($file);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($file);
        $file    = $this->__changeNodePathFromString($file, $pathStr);

        return $this->gear()->getFileSize($file);
    }

    /**
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
        $pathStr = $this->__getRealPathFromIsolatedPath($file);
        $file    = $this->__changeNodePathFromString($file, $pathStr);

        return $this->gear()->flock($file, $lock);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($file);
        $file    = $this->__changeNodePathFromString($file, $pathStr);

        return $this->gear()->isReadable($file);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($file);
        $file    = $this->__changeNodePathFromString($file, $pathStr);

        return $this->gear()->isWritable($file);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($dir);
        $dir     = $this->__changeNodePathFromString($dir, $pathStr);

        return $this->gear()->mkDir($dir, $mode);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($file);
        $file    = $this->__changeNodePathFromString($file, $pathStr);

        return $this->gear()->getFilename($file);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($file);
        $file    = $this->__changeNodePathFromString($file, $pathStr);

        return $this->gear()->getFileExtension($file);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($file);
        $file    = $this->__changeNodePathFromString($file, $pathStr);

        return $this->gear()->getBasename($file);
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
        $newName = $this->__getRealPathFromIsolatedPath($newName);

        $fRealPath = $this->__getRealPathFromIsolatedPath($file);
        $file      = $this->__changeNodePathFromString($file, $fRealPath);

        return $this->gear()->rename($file, $newName);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($dir);
        $dir     = $this->__changeNodePathFromString($dir, $pathStr);

        return $this->gear()->rmDir($dir);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($file);
        $file    = $this->__changeNodePathFromString($file, $pathStr);

        return $this->gear()->chFileATime($file, $time);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($file);
        $file    = $this->__changeNodePathFromString($file, $pathStr);

        return $this->gear()->chFileMTime($file, $time);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($link);
        $link    = $this->__changeNodePathFromString($link, $pathStr);

        $return  = $this->gear()->linkRead($link);

        return $this->__getIsolatedPathNode($return);
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
        $pathStr = $this->__getRealPathFromIsolatedPath($file);
        $file    = $this->__changeNodePathFromString($file, $pathStr);

        return $this->gear()->unlink($file);
    }

    /**
     * Replace Node Path With New Path String
     *
     * @param iCommonInfo $node
     * @param string      $newPath
     *
     * @return iCommonInfo
     */
    protected function __changeNodePathFromString(iCommonInfo $node, $newPath)
    {
        // Clone Base Object So Real Object Take No Effect
        $node = clone $node;

        $node->pathUri()
            ->reset()
            ->fromArray( $this->pathUri()->parse($newPath) );

        return $node;
    }

    /**
     * Get Isolated Masked Root Path From Real Path
     *
     * @param iCommonInfo $node
     *
     * @return iCommonInfo
     */
    protected function __getIsolatedPathNode(iCommonInfo $node)
    {
        $node = clone $node;

        $nodePathStr = $this->pathUri()
            ->fromPathUri($node->pathUri())
            ->toString()
        ;

        $pathIso = (new PathJoinUri([
            'path' => $nodePathStr,
            'separator' => $this->pathUri()->getSeparator()
        ]))
            ->mask($this->getRootPath());

        if($node->pathUri()->isAbsolute())
            // /var/www/html/upload/help.pdf mask /var/www/html/upload ===> /help.pdf
            $pathIso->prepend(new PathJoinUri([ 'path' => ['',] ]));

        $pathIso = $pathIso->toString();
        $node->pathUri()
            ->reset()
            ->fromArray( $this->pathUri()->parse($pathIso) )
        ;

        return $node;
    }

    /**
     * Get Real Filesystem Path String From Isolated Path
     *
     * @param iCommonInfo|iPathFileUri|iPathJoinedUri|string $node
     *
     * @return string
     */
    protected function __getRealPathFromIsolatedPath($node)
    {
        // Achieve Path Object:
        if ($node instanceof iCommonInfo)
            $path = new PathJoinUri([
                'path'      => $node->pathUri()->toString(),
                'separator' => $node->pathUri()->getSeparator()
            ]);
        elseif (is_string($node))
            $path = new PathJoinUri([
                'path'      => $node,
                'separator' => $this->pathUri()->getSeparator()
            ]);

        // Get Isolated Real Filesystem Path To File:

        if (!$path->isAbsolute()) {
            $cwdPath = new PathJoinUri([
                'path'      => $this->getCwd()->pathUri()->toString(),
                'separator' => $this->pathUri()->getSeparator()
            ]);

            $path = $cwdPath->append($path)
                ->normalize()
            ;
        }

        $path = $this->pathUri()
            ->setBasepath($this->getRootPath())
            ->setPath($path)
            ->allowOverrideBasepath(false)
            ->normalize()
        ;

        return $path->toString();
    }
}
