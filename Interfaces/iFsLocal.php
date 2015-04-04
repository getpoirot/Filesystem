<?php
namespace Poirot\Filesystem\Interfaces;

use Poirot\Filesystem\Interfaces\Filesystem\iCommonInfo;

interface iFsLocal extends iFsBase
{
    /**
     * Gives information about a file
     *
     * @link http://php.net/manual/en/function.stat.php
     *
     * @param iCommonInfo $node
     *
     * @return array
     */
    function getStat(iCommonInfo $node);
}
