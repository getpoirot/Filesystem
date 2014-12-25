<?php
namespace Poirot\Filesystem\Interfaces;

interface iFolder extends iFolderInfo, iNode, iStorage
{
    /**
     * Delete a directory
     *
     * @return bool
     */
    function rmDir();

    /**
     * Change Current Work Dir To Folder
     *
     * @return $this
     */
    function chToDir();

    /**
     * List an array of files and directories from the directory
     *
     * @return array
     */
    function scanDir();
}
