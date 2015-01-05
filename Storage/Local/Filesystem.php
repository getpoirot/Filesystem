<?php
namespace Poirot\Filesystem\Storage\Local;

use Poirot\Filesystem\Interfaces\Filesystem\iPermissions;
use Poirot\Filesystem\Interfaces\iCommon;
use Poirot\Filesystem\Interfaces\iCommonInfo;
use Poirot\Filesystem\Interfaces\iDirectory;
use Poirot\Filesystem\Interfaces\iDirectoryInfo;
use Poirot\Filesystem\Interfaces\iFile;
use Poirot\Filesystem\Interfaces\iFileInfo;
use Poirot\Filesystem\Interfaces\iFilesystem;
use Poirot\Filesystem\Interfaces\iLink;
use Poirot\Filesystem\Interfaces\iLinkInfo;
use Poirot\Filesystem\Permissions;

/**
 * ! Note: In PHP Most Of Filesystem actions need
 *         file/directory permission be as same as
 *         apache/php user
 */
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

        $filename = $file->getRealPathName();
        // Upon failure, an E_WARNING is emitted.
        $group = @posix_getgrgid(filegroup($filename));
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

        $filename = $file->getRealPathName();
        if (!chmod($filename, $mode->getTotalPerms()))
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
        $fperm = @fileperms($file->getRealPathName());

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

        $filename = $file->getRealPathName();
        if (!chown($filename, $user))
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

        $filename = $file->getRealPathName();
        // Upon failure, an E_WARNING is emitted.
        $owner = @posix_getgrgid(fileowner($filename)); // fileowner() "root" is 0
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
            , $source->getRealPathName(), $dest->getRealPathName()
            ));

        if (!$this->isDir($dest) || !$this->isFile($dest))
            throw new \Exception(sprintf(
                'Destination at "%s" Must be a File Or Directory For Copy.'
                , $dest->getRealPathName()
            ));

        $copied = false;
        if ($this->isDir($dest)) {
            // Copy to directory
            if (!$this->isExists($dest))
                $this->mkDir($dest, new Permissions(0777));

            if ($this->isFile($source))
                $copied = copy(
                    $source->getRealPathName()
                    , $dest->getRealPathName().self::DS.$source->getRealPathName()
                );
            else {
                // Merge Folder
                $x = 1;
            }
        } else {
            // Copy File To Destination(file)

            // make directories to destination to avoid error >>> {
            $destDir = $this->getDirname(
                $dest->getRealPathName()
            );
            $destDir = new Directory($destDir);
            if (!$this->isExists($destDir))
                $this->mkDir($destDir, new Permissions(0777));
            // } <<<

            $copied = copy(
                $source->getRealPathName()
                , $dest->getRealPathName()
            );
        }

        if (!$copied)
            throw new \Exception(sprintf(
                'Failed Copy "%s" To "%s".'
                , $source->getRealPathName(), $dest->getRealPathName()
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
     * @param iCommon $source
     *
     * @return bool
     */
    function isFile(iCommon $source)
    {
        /* TODO Make Resource from path */

        return $source instanceof iFileInfo;
    }

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
    function isDir(iCommon $source)
    {
        /* TODO Make Resource from path */

        return $source instanceof iDirectoryInfo;
    }

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
    function isLink(iCommon $source)
    {
        /* TODO Make Resource from path */

        return $source instanceof iLinkInfo;
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
     * ! return FALSE for symlinks pointing to non-existing files
     *
     * @param iCommonInfo $file
     *
     * @return boolean
     */
    function isExists(iCommonInfo $file)
    {
        // Upon failure, an E_WARNING is emitted.
        $result = @file_exists($file->getRealPathName());
        clearstatcache();

        return $result;
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

        $filename = $file->getRealPathName();
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
     * Write a string to a file
     *
     * - If filename does not exist, the file is created
     *
     * ! fails if you try to put a file in a directory that doesn't exist.
     *
     * @param iFile  $file
     * @param string $contents
     * @param bool   $append   Append Content To File
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function putFileContents(iFile $file, $contents, $append = false)
    {
        $append  = ($append) ? FILE_APPEND : 0;
        $append |= LOCK_EX; // to prevent anyone else writing to the file at the same time

        $filename = $file->getRealPathName();
        if(!file_put_contents($filename, $contents, $append)) // file will be created if not exists
            throw new \Exception(sprintf(
                'Failed To Put "%s" File Contents.'
                , $filename
            ), null, new \Exception(error_get_last()['message']));

        $file->setContents($contents);

        return $this;
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

        $filename = $file->getRealPathName();
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

        $filename = $file->getRealPathName();
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

        $filename = $file->getRealPathName();
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
     * @param iFile $file
     *
     * @throws \Exception On Failure
     * @return int In bytes
     */
    function getFileSize(iFile $file)
    {
        $this->validateFile($file);

        $filename = $file->getRealPathName();
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

        $filename = $file->getRealPathName();
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
        $filename = $file->getRealPathName();
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
        $filename = $file->getRealPathName();
        // Upon failure, an E_WARNING is emitted.
        $result = @is_writable($filename);

        clearstatcache();

        return $result;
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
     * @param iDirectoryInfo $dir
     * @param iPermissions $mode
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function mkDir(iDirectoryInfo $dir, iPermissions $mode)
    {
        if (!@mkdir($dir->getRealPathName(), $mode->getTotalPerms(), true))
            throw new \Exception(sprintf(
                'Failed To Change Owner Of "%s" File.'
                , $dir->getRealPathName()
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
    function getDirname(iCommonInfo $file)
    {
        /*
         * TODO
         * dirname() is locale aware, so for it to see the correct
         * directory name with multibyte character paths, the matching
         * locale must be set using the setlocale() function.
         */
        $pathname  = $file->getRealPathName();
        $dirname   = dirname($pathname);

        $directory = new Directory($dirname);

        return $directory;
    }

    /**
     * Returns the base name of the given path.
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

        return basename($file->getRealPathName());
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

        return pathinfo($file->getRealPathName(), PATHINFO_EXTENSION);
    }

    /**
     * Get File/Folder Name Without Extension
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

        return pathinfo($file->getRealPathName(), PATHINFO_FILENAME);
    }

    /**
     * Rename File Or Directory
     *
     * @param iCommonInfo $file
     * @param string      $newName
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function rename(iCommonInfo $file, $newName)
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
 