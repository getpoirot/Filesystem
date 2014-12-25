<?php
namespace Poirot\Filesystem;

interface iLink extends iLinkInfo, iFile
{
    /**
     * Gets the target of a link
     *
     * - can be a File or Directory
     *
     * @return mixed
     */
    function setTarget();
}
