<?php
namespace Poirot\Filesystem\Interfaces;

use Poirot\Filesystem\Interfaces\Filesystem\iStream;
use Poirot\Filesystem\Interfaces\Filesystem\iStreamFile;

interface iStreamFilesystem
{
    /*++
    Stream File Open, Words Stand For:

    R = Read                 | W = Write
    -----------------------------------------------------------------------------
    A = Pointer at end       | B = Pointer at beginning
    -----------------------------------------------------------------------------
    C = Create if not exists | X = Create file only if not exists, otherwise fail
    -----------------------------------------------------------------------------
    T = Truncate file

    @see http://php.net/manual/en/function.fopen.php
    ++*/
    const STREAM_RB    = 'r';
    const STREAM_RWB   = 'r+';
    const STREAM_WBCT  = 'W';
    const STREAM_RWBCT = 'W+';
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
     * @param iStream $file File To Be Streamed
     * @param string      $mode self::STREAM_*
     *
     * @throw \Exception On Failed
     * @return iStream
     */
    function stream(iStream $file, $mode);
}
