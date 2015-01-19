<?php
namespace Poirot\Filesystem\Interfaces;

use Poirot\Filesystem\Interfaces\Filesystem\iFileStream;

interface iFilesystemStream
{
    const STREAM_RB    = 'r';
    const STREAM_RWB   = 'r+';
    const STREAM_WBTC  = 'W';
    const STREAM_RWBTC = 'W+';
    const STREAM_WAC   = 'a';
    const STREAM_RWAC  = 'a+';
    const STREAM_WBX   = 'X';
    const STREAM_RWBX  = 'X+';
    const STREAM_WBC   = 'C';
    const STREAM_RWBC  = 'C+';

    /**
     * Stream a File
     *
     * - check for supported stream wrapper from
     *   iFile scheme
     * - get resource from file, inject to iStream
     *
     * @param iFileStream $file File To Be Streamed
     * @param string      $mode self::STREAM_*
     *
     * @throw \Exception On Failed
     * @return iStream
     */
    function stream(iFileStream $file, $mode);
}
