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

    /**
     * Gets the file size in bytes for the file referenced
     *
     * @return int
     */
    function getSize();
}
