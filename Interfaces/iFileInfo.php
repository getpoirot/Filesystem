<?php
namespace Poirot\Filesystem;

interface iFileInfo extends iNodeInfo
{
    /**
     * Gets the file extension
     *
     * @return string
     */
    function getExtension();
}
