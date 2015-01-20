<?php
namespace Poirot\Filesystem\Abstracts;

use Poirot\Filesystem\Interfaces\Filesystem\iDirectory;
use Poirot\Filesystem\Interfaces\Filesystem\iDirectoryInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iFile;
use Poirot\Filesystem\Interfaces\Filesystem\iFileInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iLink;
use Poirot\Filesystem\Interfaces\Filesystem\iPermissions;

class Link extends Common
    implements
    iLink
{
    /**
     * @var iDirectoryInfo|iFileInfo
     */
    protected $target;

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
    function dirUp()
    {
        return $this->filesystem()->dirUp($this);
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

    /**
     * Gets the target of a link
     *
     * @param iFile|iDirectory $target Target
     *
     * @throws \Exception
     * @return $this
     */
    function setTarget($target)
    {
        if (!$target instanceof iDirectoryInfo
            || !$target instanceof iFileInfo
        )
            throw new \Exception(sprintf(
                'Target must instance of "iDirectoryInfo" or "iFileInfo" but "%s" given.'
            ), is_object($target) ? get_class($target) : gettype($target));

        $this->target = $target;

        return $this;
    }

    /**
     * Gets the target of a link
     *
     * - can be a File or Directory
     *
     * @return iFile|iDirectory
     */
    function getTarget()
    {
        return $this->target;
    }
}
