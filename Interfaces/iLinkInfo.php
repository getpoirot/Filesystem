<?php
namespace Poirot\Filesystem;

interface iLinkInfo extends iFileInfo
{
    /**
     * Gets the target of a link
     *
     * - can be a File or Directory
     *
     * @return mixed
     */
    function getTarget();
}
