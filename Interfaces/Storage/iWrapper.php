<?php
namespace Poirot\Filesystem\Interfaces\Storage;

use Poirot\Filesystem\iStorage;

interface iWrapper
{
    /**
     * Construct
     *
     * @param iStorage $storage
     */
    function __construct(iStorage $storage);
}
