<?php
namespace Poirot\Filesystem\Abstracts;

use Poirot\Core\BuilderSetterTrait;
use Poirot\Filesystem\Interfaces\Filesystem\iDirectory;
use Poirot\Filesystem\Interfaces\Filesystem\iPermissions;
use Poirot\Filesystem\Interfaces\iFilesystem;
use Poirot\Filesystem\Interfaces\iFilesystemAware;
use Poirot\Filesystem\Interfaces\iFilesystemProvider;
use Poirot\Filesystem\Local\Filesystem;
use Poirot\Filesystem\Permissions;
use Poirot\Filesystem\Util;

class Directory
    implements
    iDirectory,
    iFilesystemAware,
    iFilesystemProvider
{
    use BuilderSetterTrait;

    protected $filesystem;

    protected $filename;
    protected $path;

    /**
     * Construct
     *
     * @param array $setterBuilder
     */
    function __construct(array $setterBuilder = [])
    {
       if (!empty($setterBuilder))
           $this->setupFromArray($setterBuilder);
    }

    /**
     * Set Basename of file or folder
     *
     * ! without extension
     *
     * - /path/to/filename[.ext]
     * - /path/to/folderName/
     *
     * @param string $name Basename
     *
     * @return $this
     */
    function setFilename($name)
    {
        $this->filename = $name;

        return $this;
    }

    /**
     * Gets the base name of the file
     *
     * - Include extension on files
     *
     * @return string
     */
    function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set Path
     *
     * - trimmed left /\ path
     * - it's consumed from cwd of filesystem or storage
     *
     * @param string|null $path Path To File/Folder
     *
     * @return $this
     */
    function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Gets the path without filename
     *
     * - Get CWDir (Filesystem) If Path Not Set
     *
     * @return string
     */
    function getPath()
    {
        if ($this->path === null)
            $this->setPath(
                $this->filesystem()->getCwd()->getRealPathName()
            );

        return Util::normalizePath($this->path);
    }

    /**
     * Get Path Name To File Or Folder
     *
     * - include full path for remote files
     * - include extension for files
     *
     * @return string
     */
    function getRealPathName()
    {
        return $this->getPath().'/'.$this->getFilename();
    }

    /**
     * Makes directory Recursively
     *
     * @return $this
     */
    function mkDir()
    {
        $this->filesystem()->mkDir($this
            , new Permissions(0755)
        );

        return $this;
    }

    /**
     * Set Owner
     *
     * @param int $owner
     *
     * @return $this
     */
    function chown($owner)
    {
        $this->filesystem()->chown($this, $owner);

        return $this;
    }

    /**
     * Gets the owner of the file
     *
     * @return mixed
     */
    function getOwner()
    {
        return $this->filesystem()->getFileOwner($this);
    }

    /**
     * Changes file mode
     *
     * @param iPermissions $mode
     *
     * @return $this
     */
    function chmod(iPermissions $mode)
    {
        $this->filesystem()->chmod($this, $mode);

        return $this;
    }

    /**
     * Gets file permissions
     * Should return an or combination of the PERMISSIONS
     *
     * exp. from storage WRITABLE|EXECUTABLE
     *
     * @return iPermissions
     */
    function getPerms()
    {
        return $this->filesystem()->getFilePerms($this);
    }

    /**
     * Set Group
     *
     * @param $group
     *
     * @return $this
     */
    function chgrp($group)
    {
        $this->filesystem()->chgrp($this, $group);

        return $this;
    }

    /**
     * Gets the file group
     *
     * @return mixed
     */
    function getGroup()
    {
        return $this->filesystem()->getFileGroup($this);
    }

    /**
     * Returns parent directory's path
     *
     * /etc/passwd => /etc
     *
     * @return iDirectory
     */
    function getDirname()
    {
        return $this->filesystem()->getDirname($this);
    }

    /**
     * Delete a directory from storage
     *
     * @return bool
     */
    function rmDir()
    {
        $this->filesystem()->rmDir($this);
    }

    /**
     * Copy to new directory
     *
     * - Merge if directory exists
     * - Create If Directory Not Exists
     *
     * @param iDirectory $directory
     *
     * @return $this
     */
    function copy(iDirectory $directory)
    {
        $this->filesystem()->copy($this, $directory);

        return $this;
    }

    /**
     * Move to new directory
     *
     * ! use class copy/rmDir
     *
     * - Merge if directory exists
     * - Create If Directory Not Exists
     * - Use Temp Folder For Safe Move
     *
     * @param iDirectory $directory
     *
     * @return $this
     */
    function move(iDirectory $directory)
    {

    }

    /**
     * List an array of files/directories Object from the directory
     *
     * @return array
     */
    function scanDir()
    {
        return $this->filesystem()->scanDir($this);
    }

    /**
     * Set Filesystem
     *
     * @param iFilesystem $filesystem
     *
     * @return $this
     */
    function setFilesystem(iFilesystem $filesystem)
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    /**
     * @return iFilesystem
     */
    function Filesystem()
    {
        if (!$this->filesystem)
            $this->filesystem = new Filesystem();

        return $this->filesystem;
    }

    /**
     * Is File/Folder Exists?
     *
     * @return bool
     */
    function isExists()
    {
        return $this->Filesystem()->isExists($this);
    }

    /**
     * Tells if the entry is writable
     *
     * - The writable beside of filesystem must
     *   implement iWritable
     *
     * @return bool
     */
    function isWritable()
    {
        return $this->filesystem()->isWritable($this);
    }

    /**
     * Tells if file is readable
     *
     * @return bool
     */
    function isReadable()
    {
        return $this->filesystem()->isReadable($this);
    }
}
