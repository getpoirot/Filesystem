<?php
namespace Poirot\Filesystem;

interface iFolder extends iFolderInfo, iNode, iStorage
{
    /**
     * Delete a directory
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
