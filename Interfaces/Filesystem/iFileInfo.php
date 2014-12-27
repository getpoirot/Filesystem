<?php
namespace Poirot\Filesystem\Interfaces;

interface iFileInfo extends iCommonInfo
{
    /**
     * Gets the file extension
     *
     * @return string
     */
    function getExtension();
}
