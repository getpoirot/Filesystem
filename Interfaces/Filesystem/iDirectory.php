<?php
namespace Poirot\Filesystem\Interfaces;

interface iDirectory extends iCommon, iDirectoryInfo, iStorage, iWritable
{
    /**
     * Delete a directory from storage
     *
     * @return bool
     */
    function rmDir();

    /**
     * List an array of files and directories from the directory
     *
     * @return array
     */
    function scanDir();
}
