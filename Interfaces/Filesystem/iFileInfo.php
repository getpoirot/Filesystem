<?php
namespace Poirot\Filesystem\Interfaces\Filesystem;

interface iFileInfo extends iCommonInfo
{
    /**
     * Gets the file size in bytes for the file referenced
     *
     * @return int
     */
    function getSize();
}
