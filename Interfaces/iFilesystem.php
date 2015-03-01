<?php
namespace Poirot\Filesystem\Interfaces;

use Poirot\Filesystem\Interfaces\Filesystem\iCommonInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iFileInfo;

interface iFilesystem extends iWritable
{
    const DS = DIRECTORY_SEPARATOR; // prefer to use '/' as a portable separator

    /**
     * Returns the base filename of the given path.
     *
     * @param iCommonInfo $file
     *
     * @return string
     */
    function getFilename(iCommonInfo $file);

    /**
     * Get Extension Of File
     *
     * ! empty screen if dose`nt have ext
     *
     * @param iFileInfo $file
     *
     * @return string
     */
    function getFileExtension(iFileInfo $file);

    /**
     * Get File/Folder Name Without Extension
     *
     * @param iCommonInfo $file
     *
     * @return string
     */
    function getBasename(iCommonInfo $file);
}
