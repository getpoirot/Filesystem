<?php
namespace Poirot\Filesystem\Interfaces;

interface iFile extends iCommon, iFileInfo, iWritable
{
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
     * Reads entire file into a string
     *
     * ! check permissions, getPerms
     *
     * @return string
     */
    function getContents();

    /**
     * Set File Contents
     *
     * ! check permissions, getPerms
     *
     * @param string $contents Contents
     *
     * @return $this
     */
    function setContents($contents);

    /**
     * Put File Contents to Storage
     *
     * @param string $content Content
     *
     * @return $this
     */
    function putContents($content);

    /**
     * Copy to new file
     *
     * - If Directory Given Copy With Same Name To Directory
     *
     * @param iFile|iDirectory $fileFolder
     *
     * @throws \Exception Throw Exception If File Exists
     * @return $this
     */
    function copy($fileFolder);

    /**
     * Move to new file
     *
     * - If Directory Given Copy With Same Name To Directory
     *
     * @param iFile|iDirectory $fileFolder
     *
     * @throws \Exception Throw Exception If File Exists
     * @return $this
     */
    function move($fileFolder);

    /**
     * Deletes a file from storage
     *
     * @return bool
     */
    function unlink();
}
