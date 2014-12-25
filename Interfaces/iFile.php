<?php
namespace Poirot\Filesystem;

interface iFile extends iFileInfo, iNode
{
    /**
     * Set the file extension
     *
     * @param string|null $ext File Extension
     *
     * @return $this
     */
    function setExtension($ext);

    /**
     * Reads entire file into a string
     *
     * @return string
     */
    function getContents();

    /**
     * Put Contents To File
     *
     * @param string $content Content
     *
     * @return $this
     */
    function putContents($content);

    /**
     * Rename File
     *
     * @param string $newname New name
     *
     * @return $this
     */
    function rename($newname);

    /**
     * Copy to new file
     *
     * @param iFile $file
     *
     * @return $this
     */
    function copy(iFile $file);

    /**
     * Deletes a file
     *
     * @return bool
     */
    function unlink();
}
