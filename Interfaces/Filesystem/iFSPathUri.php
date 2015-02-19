<?php
namespace Poirot\Filesystem\Interfaces\Filesystem;

/**
 * Represent Full Path/uri/to/filename.ext
 *
 */
interface iFSPathUri
{
    /**
     * Build Object From String
     *
     * @param string $pathUri
     *
     * @return $this
     */
    function fromString($pathUri);

    /**
     * Build Object From Array
     *
     * @param array $path
     *
     * @return $this
     */
    function fromArray(array $path);

    /**
     * Build Object From PathUri
     *
     * @param iFSPathUri $path
     *
     * @return $this
     */
    function fromPathUri(iFSPathUri $path);

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
     * - in form of ['path', 'to', 'dir']
     *
     * @param array|string $path Path To File/Folder
     *
     * @return $this
     */
    function setPath($path);

    /**
     * Gets the path without filename
     *
     * - return in form of ['path', 'to', 'dir']
     *
     * @return array
     */
    function getPath();

    /**
     * Get Imploded Path
     *
     * @return string
     */
    function getImPath();

    /**
     * Get Path Name To File Or Folder
     *
     * - include full path for remote files
     * - include extension for files
     *
     * @return string
     */
    function getRealPathName();

    /**
     * Get PathUri Object As String
     *
     * @return string
     */
    function toString();

    /**
     * Get Array In Form Of PathInfo
     *
     * return [
     *  'path'      => ['path', 'to', 'dir'],
     *  'impath'    => 'path/to/dir',
     *  'basename'  => 'name_with', # without extension
     *  'extension' => 'ext',
     *  'filename'  => 'name_with.ext',
     * ]
     *
     * @return array
     */
    function toArray();
}
