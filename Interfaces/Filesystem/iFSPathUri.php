<?php
namespace Poirot\Filesystem\Interfaces\Filesystem;

/**
 * Represent Full Path/uri/to/filename.ext
 *
 */
interface iFSPathUri
{
    /**
     * Set Filename of file or folder
     *
     * ! without extension
     *
     * - /path/to/filename[.ext]
     * - /path/to/folderName/
     *
     * @param string $name Basename
     *
     * @return $this
     */
    function setBasename($name);

    /**
     * Gets the file name of the file
     *
     * - Without extension on files
     *
     * @return string
     */
    function getBasename();

    /**
     * Set the file extension
     *
     * ! throw exception if file is lock
     *
     * @param string|null $ext File Extension
     *
     * @return $this
     */
    function setExtension($ext);

    /**
     * Gets the file extension
     *
     * @return string
     */
    function getExtension();

    /**
     * Get Filename Include File Extension
     *
     * ! It's a combination of basename+'.'.extension
     *   combined with a dot
     *
     * @return string
     */
    function getFilename();

    /**
     * Set Path
     *
     * @param string|null $path Path To File/Folder
     *
     * @return $this
     */
    function setPath($path);

    /**
     * Gets the path without filename
     *
     * @return string
     */
    function getPath();

    /**
     * Get Path Name To File Or Folder
     *
     * - include full path for remote files
     * - include extension for files
     * - usually use Util::normalizePath on return
     *
     * @return string
     */
    function getRealPathName();

    /**
     * Alias of getRealPathName
     *
     * @return string
     */
    function toString();
}
