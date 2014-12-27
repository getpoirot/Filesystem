<?php
namespace Poirot\Filesystem\Interfaces;

interface iLinkInfo extends iNodeInfo
{
    /**
     * Gets the target of a link
     *
     * - can be a File or Directory
     *
     * @return iFile|iFolder
     */
    function getTarget();
}
