<?php
namespace Poirot\Filesystem\Interfaces;

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

    /**
     * Make File/Folder if not exists
     *
     * @return bool
     */
    function mkIfNotExists();
}
