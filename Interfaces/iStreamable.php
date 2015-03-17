<?php
namespace Poirot\Filesystem\Interfaces;

use Poirot\Filesystem\Interfaces\Filesystem\iFileInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iStreamHandle;

interface iStreamable
{
    /**
     * Stream a File
     *
     * - check for supported stream wrapper from
     *   iFile scheme
     * - get resource from file, inject to iStream
     *
     * @param iStreamHandle $stream File To Be Streamed
     *
     * @throw \Exception On Failed
     * @return iStreamHandle
     */
    function stream(iStreamHandle $stream);

    /**
     * Make Stream From File Location Resource
     *
     * @param iFileInfo $file
     * @param string    $mode   self::STREAM_*
     *
     * @return iStreamHandle
     */
    function mkStreamFromSource(iFileInfo $file, $mode);
}
