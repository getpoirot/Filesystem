<?php
namespace Poirot\Filesystem\Interfaces;

use Poirot\Filesystem\Interfaces\Filesystem\iFileInfo;
use Poirot\Stream\Interfaces\Context\iSContext;
use Poirot\Stream\Interfaces\iSResource;
use Poirot\Stream\Interfaces\Resource\iSRAccessMode;

interface iFsStreamable
{
    /**
     * Make Stream Resource From File Location
     *
     * - create stream resource from file location
     *
     * @param iFileInfo                     $file
     * @param iSRAccessMode|string          $openMode Open mode
     * @param iSContext|array|resource|null $context  Context Options
     *
     * @return iSResource
     */
    function mkStreamFrom(iFileInfo $file, $openMode = iSRAccessMode::MODE_RB, $context = null);
}
