<?php
namespace Poirot\Filesystem\Interfaces;

use Poirot\Filesystem\Interfaces\Filesystem\iFileInfo;
use Poirot\Stream\Interfaces\iSResource;
use Poirot\Stream\Interfaces\Resource\iSRAccessMode;

interface iFsStreamable
{
    /**
     * Make Stream Resource From File Location
     *
     * - create stream resource from file location
     *
     * @param iFileInfo     $file
     * @param iSRAccessMode $mode Open mode
     *
     * @return iSResource
     */
    function mkStreamFrom(iFileInfo $file, iSRAccessMode $mode = null);
}
