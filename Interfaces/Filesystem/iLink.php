<?php
namespace Poirot\Filesystem\Interfaces\Filesystem;

interface iLink extends iLinkInfo
{
    /**
     * Gets the target of a link
     *
     * @param iFile|iDirectory $target Target
     *
     * @return $this
     */
    function setTarget($target);
}
