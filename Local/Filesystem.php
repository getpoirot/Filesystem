<?php
namespace Poirot\Filesystem\Local;

use Poirot\Filesystem\Abstracts\Directory;
use Poirot\Filesystem\Abstracts\File;
use Poirot\Filesystem\Abstracts\PathUri;
use Poirot\Filesystem\Interfaces\Filesystem\iCommon;
use Poirot\Filesystem\Interfaces\Filesystem\iCommonInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iDirectory;
use Poirot\Filesystem\Interfaces\Filesystem\iDirectoryInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iFile;
use Poirot\Filesystem\Interfaces\Filesystem\iFileInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iLinkInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iPermissions;
use Poirot\Filesystem\Interfaces\iFilesystem;
use Poirot\Filesystem\Permissions;
use Poirot\Filesystem\Util;

/**
 * ! Note: In PHP Most Of Filesystem actions need
 *         file/directory permission be as same as
 *         apache/php user
 */
class Filesystem implements iFilesystem
{
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
        $return = false;

        if ($this->isDir($path))
            $return = new Directory($path);
        elseif ($this->isFile($path))
            $return = new File($path);

        if (!$return)
            throw new \Exception(sprintf(
                    'Path "%s" not recognized.'
                    , $path
                ), null, new \Exception(error_get_last()['message'])
            );

        $return->setFilesystem($this);

        return $return;
    }

    // Directory Implementation:

    /**
     * Gets the current working directory
     *
     * @throws \Exception On Failure
     * @return iDirectory
     */
    function getCwd()
    {
        $result = getcwd();
        if ($result === false)
            throw new \Exception(
                'Failed To Get Current Working Directory.'
                , null
                , new \Exception(error_get_last()['message'])
            );

        return $this->mkFromPath($result);
    }

    /**
     * List an array of files/directories path from the directory
     *
     * - get rid of ".", ".." from list
     * - append scanned directory as path to files list
     *   [/path/to/scanned/dir/]file.ext
     *
     * @param iDirectoryInfo|null $dir          If Null Scan Current Working Directory
     * @param int                 $sortingOrder SCANDIR_SORT_NONE|SCANDIR_SORT_ASCENDING|SCANDIR_SORT_DESCENDING
     *
     * @throws \Exception On Failure
     * @return array
     */
    function scanDir(iDirectoryInfo $dir = null, $sortingOrder = self::SCANDIR_SORT_NONE)
    {
        if ($dir === null)
            $dir = $this->getCwd();

        $this->validateFile($dir);

        $dirname = $dir->filePath()->toString();
        $result  = scandir($dirname, $sortingOrder);
        if ($result === false)
            throw new \Exception(sprintf(
                'Failed Scan Directory To "%s".'
                , $dirname
            ), null, new \Exception(error_get_last()['message']));

        // get rid of the dots
        $result = array_diff($result, array('..', '.'));

        // append dir path to files
        array_walk($result, function(&$value, $key) use ($dirname)  {
            $value = PathUri::normalizePath($dirname.'/'.$value);
        });

        return $result;
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
        $this->validateFile($dir);

        $dirname = $dir->filePath()->toString();
        if (chdir($dirname) === false)
            throw new \Exception(sprintf(
                'Failed Changing Directory To "%s".'
                , $dirname
            ), null, new \Exception(error_get_last()['message']));

        return $this;
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
        $this->validateFile($file);

        $filename = $file->filePath()->toString();
        if (!@chgrp($filename, $group))
            throw new \Exception(sprintf(
                'Failed Changing Group Of "%s" File.'
                , $filename
            ), null, new \Exception(error_get_last()['message']));

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

        $filename = $file->filePath()->toString();
        // Upon failure, an E_WARNING is emitted.
        $group = filegroup($filename);
        (!function_exists('posix_getgrgid')) ?:
            $group = posix_getgrgid($group);

        if (!$group)
            throw new \Exception(sprintf(
                'Failed To Know Group Of "%s" File.'
                , $filename
            ), null, new \Exception(error_get_last()['message']));

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

        $filename = $file->filePath()->toString();
        if (!@chmod($filename, $mode->getTotalPerms()))
            throw new \Exception(sprintf(
                'Failed To Change File Mode For "%s".'
                , $filename
            ), null, new \Exception(error_get_last()['message']));

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

        // Upon failure, an E_WARNING is emitted.
        $fperm = @fileperms($file->filePath()->toString());

        $perms = new Permissions();
        $perms->grantPermission($fperm);

        return $perms;
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
        $this->validateFile($file);

        $filename = $file->filePath()->toString();
        if (!@chown($filename, $user))
            throw new \Exception(sprintf(
                'Failed To Change Owner Of "%s" File.'
                , $filename
            ), null, new \Exception(error_get_last()['message']));

        return $this;
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
        $this->validateFile($file);

        $filename = $file->filePath()->toString();
        // Upon failure, an E_WARNING is emitted.
        $owner = @fileowner($filename); // fileowner() "root" is 0
        if (function_exists('posix_getgrgid'))
            $owner = posix_getgrgid($owner);

        if (!$owner)
            throw new \Exception(sprintf(
                'Failed To Know Group Of "%s" File.'
                , $filename
            ), null, new \Exception(error_get_last()['message']));

        clearstatcache(); // don't cache result

        return $owner;
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
        // source must be valid
        $this->validateFile($source);

        if ($this->isDir($source) && !$this->isDir($dest))
            throw new \Exception(sprintf(
                'Invalid Destination Provided, We Cant Copy A Directory "%s" To File "%s".'
                , $source->filePath()->toString(), $dest->filePath()->toString()
            ));

        if (!$this->isDir($dest) && !$this->isFile($dest))
            throw new \Exception(sprintf(
                'Destination at "%s" Must be a File Or Directory For Copy.'
                , $dest->filePath()->toString()
            ));

        $copied = false;
        if ($this->isDir($dest)) {
            // Copy to directory
            if (!$this->isExists($dest))
                $this->mkDir($dest, new Permissions(0755));

            if ($this->isFile($source))
                /** @var iFile $source */
                $copied = @copy(
                    $source->filePath()->toString()
                    , $dest->filePath()->toString().'/'.$source->filePath()->getFilename()
                );
            else {
                // Merge Folder
                $destDirName = $dest->filePath()->toString().'/'.$source->filePath()->getFilename();
                $copied = true; // we don't want rise error from here
                foreach($this->scanDir($source) as $fd)
                    $this->copy(
                        $this->mkFromPath($fd)
                        , new Directory($destDirName)
                    );
            }
        } else {
            // Copy File To Destination(file)

            // make directories to destination to avoid error >>> {
            $destDir = $this->dirUp($dest);
            if (!$this->isExists($destDir))
                $this->mkDir($destDir, new Permissions(0777));
            // } <<<

            $copied = copy(
                $source->filePath()->toString()
                , $dest->filePath()->toString()
            );
        }

        if (!$copied)
            throw new \Exception(sprintf(
                'Error While Coping "%s" To "%s".'
                , $source->filePath()->toString(), $dest->filePath()->toString()
            ), null, new \Exception(error_get_last()['message']));

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

        if (is_string($source))
            $return = @is_file($source);

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

        if (is_string($source))
            $return = @is_dir($source);

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

        if (is_string($source))
            $return = @is_link($source);

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
        $result = @disk_free_space(
            $this->getCwd()->filePath()->toString()
        );
        if ($result === false)
            $result = self::DISKSPACE_UNKNOWN;

        return $result;
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
        $result = @disk_total_space(
            $this->getCwd()->filePath()->toString()
        );
        if ($result === false)
            $result = self::DISKSPACE_UNKNOWN;

        return $result;
    }

    /**
     * Checks whether a file or directory exists
     *
     * ! return FALSE for symlinks pointing to non-existing files
     *
     * @param iCommonInfo $file
     *
     * @return boolean
     */
    function isExists(iCommonInfo $file)
    {
        // Upon failure, an E_WARNING is emitted.
        $result = @file_exists($file->filePath()->toString());
        clearstatcache();

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
        $append  = ($append) ? FILE_APPEND : 0;
        $append |= LOCK_EX; // to prevent anyone else writing to the file at the same time

        $filename = $file->filePath()->toString();
        if(!file_put_contents($filename, $contents, $append)) // file will be created if not exists
            throw new \Exception(sprintf(
                'Failed To Put "%s" File Contents.'
                , $filename
            ), null, new \Exception(error_get_last()['message']));

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
        $this->validateFile($file);

        $filename = $file->filePath()->toString();
        // Upon failure, an E_WARNING is emitted.
        $content = @file_get_contents($filename);
        if ($content === false)
            throw new \Exception(sprintf(
                'Failed To Read Contents Of "%s" File.'
                , $filename
            ), null, new \Exception(error_get_last()['message']));

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
        $this->validateFile($file);

        $filename = $file->filePath()->toString();
        // Upon failure, an E_WARNING is emitted.
        $result = @fileatime($filename);
        if ($result === false)
            throw new \Exception(sprintf(
                'Failed To Get Access Time For "%s" File.'
                , $filename
            ), null, new \Exception(error_get_last()['message']));

        clearstatcache();

        return $result;
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
        // Note that on Windows systems, filectime will show the file creation time

        $this->validateFile($file);

        $filename = $file->filePath()->toString();
        // Upon failure, an E_WARNING is emitted.
        $result = @filectime($filename);
        if ($result === false)
            throw new \Exception(sprintf(
                'Failed To Get Change Time For "%s" File.'
                , $filename
            ), null, new \Exception(error_get_last()['message']));

        clearstatcache();

        return $result;
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
        $this->validateFile($file);

        $filename = $file->filePath()->toString();
        // Upon failure, an E_WARNING is emitted.
        $result = @filemtime($filename);
        if ($result === false)
            throw new \Exception(sprintf(
                'Failed To Get Modified Time For "%s" File.'
                , $filename
            ), null, new \Exception(error_get_last()['message']));

        clearstatcache();

        return $result;
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
        $this->validateFile($file);

        $filename = $file->filePath()->toString();
        // Upon failure, an E_WARNING is emitted.
        $result = @filesize($filename);
        if ($result === false)
            throw new \Exception(sprintf(
                'Failed To Get Size Of "%s" File.'
                , $filename
            ), null, new \Exception(error_get_last()['message']));

        clearstatcache();

        return $result;
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
        $this->validateFile($file);

        $filename = $file->filePath()->toString();
        $fp = fopen($filename, "r+");

        // Upon failure, an E_WARNING is emitted.
        $result = @flock($fp, $lock);
        if ($result === false)
            throw new \Exception(sprintf(
                'Failed To Lock "%s" File.'
                , $filename
            ), null, new \Exception(error_get_last()['message']));

        return $result;
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
        $filename = $file->filePath()->toString();
        // Upon failure, an E_WARNING is emitted.
        $result = @is_readable($filename);

        clearstatcache();

        return $result;
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
        $filename = $file->filePath()->toString();
        // Upon failure, an E_WARNING is emitted.
        $result = @is_writable($filename);

        clearstatcache();

        return $result;
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
        $target = $link->getTarget();

        $this->validateFile($target);

        $filename = $link->filePath()->toString();
        // Upon failure, an E_WARNING is emitted.
        $result = @link($target->filePath()->toString(), $filename);
        if ($result === false)
            throw new \Exception(sprintf(
                'Failed To Create "%s" Link.'
                , $filename
            ), null, new \Exception(error_get_last()['message']));

        return $result;
    }

    /**
     * Makes directory Recursively
     *
     * @param iDirectoryInfo $dir
     * @param iPermissions $mode
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function mkDir(iDirectoryInfo $dir, iPermissions $mode)
    {
        if (!@mkdir($dir->filePath()->toString(), $mode->getTotalPerms(), true))
            throw new \Exception(sprintf(
                'Failed To Make Directory "%s".'
                , $dir->filePath()->toString()
            ), null, new \Exception(error_get_last()['message']));

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
        /*
         * TODO
         * dirname() is locale aware, so for it to see the correct
         * directory name with multibyte character paths, the matching
         * locale must be set using the setlocale() function.
         */
        $dirname   = $file->filePath()->getPath();
        $directory = $this->mkFromPath($dirname);

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
        /*
         * TODO
         * basename() is locale aware, so for it to see the correct
         * directory name with multibyte character paths, the matching
         * locale must be set using the setlocale() function.
         */

        return basename($file->filePath()->toString());
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
        /*
         * TODO
         * basename() is locale aware, so for it to see the correct
         * directory name with multibyte character paths, the matching
         * locale must be set using the setlocale() function.
         */

        return pathinfo($file->filePath()->toString(), PATHINFO_EXTENSION);
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
        /*
         * TODO
         * basename() is locale aware, so for it to see the correct
         * directory name with multibyte character paths, the matching
         * locale must be set using the setlocale() function.
         */

        return pathinfo($file->filePath()->toString(), PATHINFO_FILENAME);
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
        $pathInfo = PathUri::getPathInfo($newName);
        if (!isset($pathInfo['path']))
            $newName = $this->dirUp($file)->filePath()->toString()
                .'/'. $newName;

        if (!@rename($file->filePath()->toString(), $newName))
            throw new \Exception(sprintf(
                'Failed To Rename "%s" File.'
                , $file->filePath()->toString()
            ), null, new \Exception(error_get_last()['message']));

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
        $this->validateFile($dir);

        $lsDir = $this->scanDir($dir);
        if (!empty($lsDir))
            foreach($lsDir as $ls) {
                // First: Delete Directories Recursively
                $node = $this->mkFromPath($ls);
                if ($this->isDir($node))
                    $this->rmDir($node);
                else
                    $this->unlink($node);
            }

        // Ensure That Folder Is Empty: Delete It
        if (!@rmdir($dir->filePath()->toString()))
            throw new \Exception(sprintf(
                'Error While Deleting "%s" File.'
                , $dir->filePath()->toString()
            ), null, new \Exception(error_get_last()['message']));

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
        $this->validateFile($file);

        $filename = $file->filePath()->toString();
        // Upon failure, an E_WARNING is emitted.
        $result = @touch($filename, null, $time);
        if ($result === false)
            throw new \Exception(sprintf(
                'Failed To Touch "%s" File.'
                , $filename
            ), null, new \Exception(error_get_last()['message']));

        return $this;
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
        $this->validateFile($file);

        $filename = $file->filePath()->toString();
        // Upon failure, an E_WARNING is emitted.
        $result = @touch($filename, $time);
        if ($result === false)
            throw new \Exception(sprintf(
                'Failed To Touch "%s" File.'
                , $filename
            ), null, new \Exception(error_get_last()['message']));

        return $this;
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
        $filename = $link->filePath()->toString();
        $result = readlink($filename);
        if ($result === false)
            throw new \Exception(sprintf(
                'Failed To Read Link From "%s" File.'
                , $filename
            ), null, new \Exception(error_get_last()['message']));

        return $this->mkFromPath($result);
    }

    /**
     * Deletes a file
     *
     * @param iFileInfo $file
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function unlink(iFileInfo $file)
    {
        $this->validateFile($file);

        $filename = $file->filePath()->toString();
        // Upon failure, an E_WARNING is emitted.
        $result = @unlink($filename);
        if ($result === false)
            throw new \Exception(sprintf(
                'Failed To Delete "%s" File.'
                , $filename
            ), null, new \Exception(error_get_last()['message']));

        return $this;
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
        $filename = $file->filePath()->toString();

        if (!$this->isFile($file) && !$this->isDir($file))
            throw new \Exception(sprintf(
                'The Destination File "%s" Must Be a File Or Folder.'
                , $filename
            ));
        elseif (!$this->isExists($file))
            throw new \Exception(sprintf(
                'File "%s" Not Found.'
                , $filename
            ));
    }
}
 