<?php
namespace Poirot\Filesystem\Interfaces;

interface iFile extends iCommon, iFileInfo, iWritable
{
    /**
     * Lock File
     *
     * @return $this
     */
    function lock();

    /**
     * Unlock file
     *
     * @return $this
     */
    function unlock();

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
     * Rename File And Write To Storage
     *
     * @param string $newname New name
     *
     * @return $this
     */
    function rename($newname);

    /**
     * Deletes a file from storage
     *
     * @return bool
     */
    function unlink();
}
