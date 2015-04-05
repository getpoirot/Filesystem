<?php
namespace Poirot\Filesystem\Adapter;

use Poirot\Filesystem\Interfaces\Filesystem\iDirectory;
use Poirot\Filesystem\Interfaces\Filesystem\iDirectoryInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iFile;
use Poirot\Filesystem\Interfaces\Filesystem\iFileInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iLink;
use Poirot\Filesystem\Interfaces\Filesystem\iFilePermissions;

class Link extends AbstractCommonNode
    implements
    iLink
{
    /**
     * @var iDirectoryInfo|iFileInfo
     */
    protected $target;

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
            && !$target instanceof iFileInfo
        )
            throw new \Exception(sprintf(
                'Target must instance of "iDirectoryInfo" or "iFileInfo" but "%s" given.'
                , is_object($target) ? get_class($target) : gettype($target)
            ));

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
