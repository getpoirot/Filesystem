<?php
namespace Poirot\Filesystem\Interfaces;

interface iFileInfo extends iNodeInfo
{
    /**
     * Gets the file extension
     *
     * @return string
     */
    function getExtension();
}
