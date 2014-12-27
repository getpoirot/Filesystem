<?php
namespace Poirot\Filesystem\Interfaces;

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
