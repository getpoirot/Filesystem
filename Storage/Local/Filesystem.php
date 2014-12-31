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
 