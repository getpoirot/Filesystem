<?php
namespace Poirot\Filesystem\Interfaces;

interface iLink extends iLinkInfo, iWritable
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