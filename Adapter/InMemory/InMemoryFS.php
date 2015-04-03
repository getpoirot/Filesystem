<?php
namespace Poirot\Filesystem\Adapter\InMemory;

use Poirot\Filesystem\Adapter\Directory;
use Poirot\Filesystem\Adapter\File;
use Poirot\Filesystem\Adapter\Link;
use Poirot\Filesystem\Exception\FileNotFoundException;
use Poirot\Filesystem\FilePermissions;
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
        '/' => [
            '__meta__' => [
                'type'       => 'dir',
                'owner'      => 'root',
                'group'      => 'root',
                'permission' => 0755,
                'atime'      => null/*time()*/,
                'mtime'      => null/*time()*/,
                'ctime'      => null/*time()*/,
            ]
            /*
            'nameOfFile' => [
                '__meta__' => [
                    'type'    => 'file',
                    'content' => '',
                ]
            ]
            */
        ]
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
        if ($seek === false || ($seek !== false && !$this->__fs_get_meta($seek, 'type') == 'dir'))
            throw new \Exception(sprintf(
                'Failed Changing Directory To "%s".'
                , $dirPath->toString()
            ));

        // Absolute paths from home for current directories
        $cwd = $this->getCwd()->pathUri()->getPath()
            ->append(new PathJoinUri([
                'path'       => $dirPath->toString(),
                'separator' => $this->pathUri()->getSeparator()
            ]))
            ->toString();

        $this->cwdPath = $cwd;

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
        // create filesystem node object
        $return = false;
        if ($this->isDir($path))
            $return = new Directory;
        elseif ($this->isFile($path))
            $return = new File;
        elseif ($this->isLink($path))
            $return = new Link;

        if (!$return)
            throw new \Exception(sprintf(
                'Cant Make From Path "%s", not recognized.'
                , $path
            ), null, new \Exception(error_get_last()['message']));

        $return
            ->setFilesystem($this)
            ->pathUri()
            ->setSeparator( $this->pathUri()->getSeparator() )
            ->fromArray(
                $this->pathUri()->parse($path)
            )
        ;

        if ($this->isLink($return))
            $return->setTarget( $this->linkRead($return) );

        return $return;
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
        if ($dir === null)
            $dir = $this->getCwd();

        // Validating Node Tree:
        $seek = &$this->__seekTreeFromPath($dir->pathUri(), true);

        if ($seek === false || ($seek !== false && !$this->__fs_get_meta($seek, 'type') == 'dir'))
            throw new \Exception(sprintf(
                'Failed Scan Directory To "%s".'
                , $dir->pathUri()->toString()
            ));

        // Get List:
        $return = []; reset($seek);
        while ($curr = current($seek)) {
            $name = key($seek);
            $name == '__meta__' ?: // filter __meta__ from directory list
                $return[] = $name;

            next($seek);
        }

        // Sort Items:
        switch($sortingOrder) {
            case self::SCANDIR_SORT_ASCENDING:
                sort($return);
                break;
            case self::SCANDIR_SORT_DESCENDING:
                rsort($return);
                break;
            case self::SCANDIR_SORT_NONE:
                // ignored
                break;
        }

        return $return;
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
        $pathUri = $this->pathUri()
            ->fromPathUri($file->pathUri());

        $seek = &$this->__seekTreeFromPath($pathUri);
        if ($seek === false)
            throw new FileNotFoundException(sprintf(
                '"%s" Not Found.'
                , $pathUri->toString()
            ));

        return $this->__fs_get_meta($seek, 'group');
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
     * @throws FileNotFoundException
     * @return iFilePermissions
     */
    function getFilePerms(iCommonInfo $file)
    {
        $pathUri = $this->pathUri()
            ->fromPathUri($file->pathUri());

        $seek = &$this->__seekTreeFromPath($pathUri);
        if ($seek === false)
            throw new FileNotFoundException(sprintf(
                '"%s" Not Found.'
                , $pathUri->toString()
            ));

        return $this->__fs_get_meta($seek, 'permission');
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
        $pathUri = $this->pathUri()
            ->fromPathUri($file->pathUri());

        $seek = &$this->__seekTreeFromPath($pathUri);
        if ($seek === false)
            throw new FileNotFoundException(sprintf(
                '"%s" Not Found.'
                , $pathUri->toString()
            ));

        return $this->__fs_get_meta($seek, 'owner');
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
        $srcPathStr =
            $this->pathUri()
            ->fromPathUri($source->pathUri())
            ->toString()
        ;

        $dstPathStr = $this->pathUri()
            ->fromPathUri($dest->pathUri())
            ->toString()
        ;

        // source must be valid
        $srcNode = &$this->__seekTreeFromPath($source->pathUri(), true);

        if ($this->isDir($source) && !$this->isDir($dest))
            throw new \Exception(sprintf(
                'Invalid Destination Provided, We Cant Copy A Directory "%s" To File "%s".'
                , $srcPathStr, $dstPathStr
            ));

        if (!$this->isDir($dest) && !$this->isFile($dest))
            throw new \Exception(sprintf(
                'Destination at "%s" Must be a File Or Directory For Copy.'
                , $dstPathStr
            ));

        if ($this->isDir($dest)) {
            // Copy File to directory
            if (!$this->isExists($dest))
                $this->mkDir($dest, new FilePermissions(0755));

            if ($this->isFile($source)) {
                $dstNode = &$this->__seekTreeFromPath(
                    $dest->pathUri()
                    ->setBasename($source->pathUri()->getBasename())
                    ->setExtension($source->pathUri()->getExtension())
                );

                $dstNode[$source->pathUri()->getFilename()] = $srcNode;
            }
            else {
                // Copy Directory To Directory
                foreach($this->scanDir($source) as $fd)
                    $this->copy(
                        $this->mkFromPath($fd)
                        , $dest
                    );
            }
        } else {
            // Copy File To Destination(file)

            // make directories to destination to avoid error >>> {
            $destDir = $this->dirUp($dest);
            if (!$this->isExists($destDir))
                $this->mkDir($destDir, new FilePermissions(0755));
            // } <<<

            $dstNode = &$this->__seekTreeFromPath($destDir->pathUri());

            $dstNode[$source->pathUri()->getFilename()] = $srcNode;
        }

        return $this;
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
        $return = false;

        if (is_string($source)) {
            $pathUri = $this->pathUri()
                ->fromArray($this->pathUri()->parse($source));

            $seek = &$this->__seekTreeFromPath($pathUri);
            if ($seek !== false)
                if ( $this->__fs_get_meta($seek, 'type') == 'file' )
                    $return = true;
        }

        if(is_object($source))
            $return = $source instanceof iFileInfo;

        return $return;
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
        $return = false;

        if (is_string($source)) {
            $pathUri = $this->pathUri()
                ->fromArray($this->pathUri()->parse($source));

            $seek = &$this->__seekTreeFromPath($pathUri);
            if ($seek !== false)
                if ( $this->__fs_get_meta($seek, 'type') == 'dir' )
                    $return = true;
        }

        if(is_object($source))
            $return = $source instanceof iDirectoryInfo;

        return $return;
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
        $return = false;

        if (is_string($source)) {
            $pathUri = $this->pathUri()
                ->fromArray($this->pathUri()->parse($source));

            $seek = &$this->__seekTreeFromPath($pathUri);
            if ($seek !== false)
                if (isset($seek['__meta__']) && isset($seek['__meta__']['type']))
                    $return = ( $seek['__meta__']['type'] == 'link' );
        }

        if(is_object($source))
            $return = $source instanceof iLinkInfo;

        return $return;
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
     * ! return FALSE for symlinks pointing to non-existing files
     *
     * @param iCommonInfo $cnode
     *
     * @return boolean
     */
    function isExists(iCommonInfo $cnode)
    {
        $seek   = $this->__seekTreeFromPath($cnode->pathUri());
        $result = ($seek !== false);

        $cnode = clone $cnode;
        $filename = $cnode
            ->pathUri()
            ->normalize()
            ->toString();

        switch ($cnode) {
            case $cnode instanceof iDirectoryInfo:
                $result = $this->isDir($filename);
                break;
            case $cnode instanceof iFileInfo:
                $result = $this->isFile($filename);
                break;
            case $cnode instanceof iLinkInfo:
                $result = $this->isLink($filename);
                break;
        }

        return $result;
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
        $dir   = $file
            ->setFilesystem($this)
            ->dirUp();

        $seek  = &$this->__seekTreeFromPath($dir->pathUri(), true);

        $filename = $file->pathUri()->getFilename();
        $seek[$filename] = [
            '__meta__' => [
                'type'       => 'file',
                'content'    => $contents,
                'owner'      => 'root',
                'group'      => 'root',
                'permission' => 0755,
                'atime'      => time(),
                'mtime'      => time(),
                'ctime'      => time(),
            ],
        ];

        $file->setContents($contents);

        return $this;
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
        $seek = &$this->__seekTreeFromPath($file->pathUri());
        if (!$this->__fs_get_meta($seek, 'type') == 'file')
            throw new \Exception(sprintf(
                'Failed To Read Contents Of "%s" File.'
                , $file->pathUri()->toString()
            ));

        $content = $this->__fs_get_meta($seek, 'content');

        return $content;
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
        $pathUri = $this->pathUri()
            ->fromPathUri($file->pathUri());

        $seek = &$this->__seekTreeFromPath($pathUri);
        if ($seek === false)
            throw new FileNotFoundException(sprintf(
                '"%s" Not Found.'
                , $pathUri->toString()
            ));

        return $this->__fs_get_meta($seek, 'atime');
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
        $pathUri = $this->pathUri()
            ->fromPathUri($file->pathUri());

        $seek = &$this->__seekTreeFromPath($pathUri);
        if ($seek === false)
            throw new FileNotFoundException(sprintf(
                '"%s" Not Found.'
                , $pathUri->toString()
            ));

        return $this->__fs_get_meta($seek, 'ctime');
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
        $pathUri = $this->pathUri()
            ->fromPathUri($file->pathUri());

        $seek = &$this->__seekTreeFromPath($pathUri);
        if ($seek === false)
            throw new FileNotFoundException(sprintf(
                '"%s" Not Found.'
                , $pathUri->toString()
            ));

        return $this->__fs_get_meta($seek, 'mtime');
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
        $content = $this->getFileContents($file);

        return mb_strlen($content, '8bit');
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
        return true;
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
        return true;
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
        // achieve link target:
        $target     = $link->getTarget();
        $targetPath = $this->pathUri()
            ->fromPathUri($target->pathUri())
        ;

        $targetSeek = &$this->__seekTreeFromPath($targetPath, true);

        // create link to target:
        $dir   = $link
            ->setFilesystem($this)
            ->dirUp();

        $seek     = &$this->__seekTreeFromPath($dir->pathUri(), true);
        $filename = $link->pathUri()->getFilename();
        $seek[$filename] = [
            '__meta__' => [
                'type'       => 'link',
                'target'     => $targetSeek,
                'owner'      => 'root',
                'group'      => 'root',
                'permission' => 0755,
                'atime'      => time(),
                'mtime'      => time(),
                'ctime'      => time(),
            ],
        ];

        return $this;
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
    function mkDir(iDirectoryInfo $dir, iFilePermissions $mode = null)
    {
        if ($mode === null)
            $mode = new FilePermissions(0755); // todo use static default permission

        $paths = $this->__parseToPathStepsArray($dir->pathUri());

        $tree = &$this->tree;
        while ($curr = array_shift($paths)) {
            if (!array_key_exists($curr, $tree))
                $tree[$curr] = [
                    '__meta__' => [
                        'type'        => 'dir',
                        'permissions' => $mode->getTotalPerms()
                    ],
                ];

            if (array_key_exists($curr, $tree))
                // ensure that not same name exists as file
                if ($tree[$curr]['__meta__']['type'] !== 'dir')
                    throw new \Exception(sprintf(
                        'cannot create directory ‘%s’: File exists',
                        $curr
                    ));
                else
                    $tree = &$tree[$curr];
        }

        return $this;
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
        $path = $this->pathUri()
            ->fromPathUri($file->pathUri())
            ->setBasename(null)
            ->setExtension(null)
            ->normalize()
            ->toString();

        $directory = $this->mkFromPath($path);

        return $directory;
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
        $pathStr = $this->pathUri()
            ->fromPathUri($file->pathUri())
            ->toString()
        ;

        return basename($pathStr);
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
        $pathStr = $this->pathUri()
            ->fromPathUri($file->pathUri())
            ->toString()
        ;

        return pathinfo($pathStr, PATHINFO_EXTENSION);
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
        $pathStr = $this->pathUri()
            ->fromPathUri($file->pathUri())
            ->toString()
        ;

        return pathinfo($pathStr, PATHINFO_FILENAME);
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
        $newPathUri = $this->pathUri()->fromArray(
            $this->pathUri()->parse($newName)
        );
        if (!$newPathUri->isAbsolute())
            // append file directory
            $newPathUri->getPath()->prepend(
                new PathJoinUri([
                    'path' => $this->dirUp($file)->pathUri()->toString(),
                    'separator' => $this->pathUri()->getSeparator()
                ])
            );

        // Make Directories path to new destination if not exists:
        $dstPath = $this->pathUri()    // only path directories to node
            ->fromPathUri($newPathUri)
            ->setBasename(null)
            ->setExtension(null)
            ->normalize()
        ;
        $this->mkDir(new Directory($dstPath->toString()), new FilePermissions(0755));

        // Rename:
        $seekSrc = &$this->__seekTreeFromPath($file->pathUri());
        $seekDst = &$this->__seekTreeFromPath($dstPath);

        $srcVal = $seekSrc;
        $seekDst[$newPathUri->getFilename()] = $srcVal;

        // Remove Previous Source Node:
        $seekSrc = &$this->__seekTreeFromPath($file->dirUp()->pathUri());
        unset($seekSrc[$file->pathUri()->getFilename()]);

        return $this;
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
        $seek    = &$this->__seekTreeFromPath($dir->dirUp()->pathUri(), true);
        $dirName = $dir->pathUri()->getFilename();
        $nodeDir = (array_key_exists($dirName, $seek))
            ? $seek[$dirName]
            : false;

        if (
            $nodeDir == false
            || !$this->__fs_get_meta($nodeDir, 'type') == 'dir'
        )
            throw new \Exception(sprintf(
                'Error While Deleting "%s" Directory.'
                , $dir->pathUri()->toString()
            ));

        unset($seek[$dirName]);

        return $this;
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
        if (!$file instanceof iFileInfo
            && !$file instanceof iLinkInfo
        )
            throw new \Exception(sprintf(
                'iFileInfo or iLinkInfo instance must given, but "%s" passed.'
                , is_object($file) ? get_class($file) : gettype($file)
            ));

        $seek     = &$this->__seekTreeFromPath($file->dirUp()->pathUri(), true);
        $fileName = $file->pathUri()->getFilename();
        $nodeFile  = (array_key_exists($fileName, $seek))
            ? $seek[$fileName]
            : false;

        if (
            $nodeFile == false
            || ! $this->__fs_get_meta($nodeFile, 'type') == 'file'
            || ! $this->__fs_get_meta($nodeFile, 'type') == 'link'
        )
            throw new \Exception(sprintf(
                'Error While Deleting "%s" File.'
                , $file->pathUri()->toString()
            ));

        unset($seek[$fileName]);

        return $this;
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
        $path = $this->pathUri()->fromPathUri($path);
        $path->normalize();

        // Initialize:
        $paths = $this->__parseToPathStepsArray($path);

        // Cached:
        $hash = (new PathJoinUri())->setPath($paths)->toString();
        if (array_key_exists($hash, $this->__cachedSeekResolve))
            return $this->__cachedSeekResolve[$hash];


        // Seek to tree:
        reset($this->tree);
        $seek = &$this->tree;
        while ($curr = array_shift($paths)) {
            if (!array_key_exists($curr, $seek)) {
                $return = false;
                return $return;
            }

            $seek = &$seek[$curr];
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
     * Given a path file object and return path
     * steps map from home to folder in array
     * ['/', 'var', 'www']
     *
     * @param iPathFileUri $path
     *
     * @return array
     */
    protected function __parseToPathStepsArray(iPathFileUri $path)
    {
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

        return $paths;
    }

    /**
     * Get Meta Attributes from node
     *
     * @param array  $node     Node Array
     * @param string $metaAttr Meta Attribute
     * @param bool   $default
     *
     * @return mixed
     */
    protected function __fs_get_meta(array $node, $metaAttr, $default = false)
    {
        $return = $default;

        if (isset($node['__meta__']) && isset($node['__meta__'][$metaAttr]))
            $return = $node['__meta__'][$metaAttr];

        return $return;
    }
}
