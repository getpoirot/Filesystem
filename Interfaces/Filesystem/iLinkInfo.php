<?php
namespace Poirot\Filesystem\Interfaces\Filesystem;

interface iLinkInfo extends iCommonInfo
{
    /**
     * Gets the target of a link
     *
     * - can be a File or Directory
     *
     * @return iFile|iDirectory
     */
    function getTarget();
}
