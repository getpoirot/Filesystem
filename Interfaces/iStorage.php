<?php
namespace Poirot\Filesystem\Interfaces;

/**
 * Storage can implement OptionsProviderInterface
 */
interface iStorage
{
    /**
     * Get Current Filesystem/Storage Working Directory
     *
     * - storage with empty working directory
     *   mean the base storage
     * - with creating files or folder cwd will
     *   append as path
     *
     * @return string
     */
    function getCwd();

    /**
     * Mount External Directory To Storage
     *
     * - The mounted directory will show on lsContent
     *
     * @param iDirectory $directory
     *
     * @return $this
     */
    function mount(iDirectory $directory);

    /**
     * UnMount Mounted Directory
     *
     * @param iDirectory $directory
     *
     * @return $this
     */
    function unmount(iDirectory $directory);

    /**
     * Is Mounted Storage?
     *
     * @return bool
     */
    function isMount();

    /**
     * List Contents
     *
     * @return array[iFile|iLink|iDirectory]
     */
    function lsContent();

    /**
     * Create new Folder Instance
     *
     * @return iDirectory
     */
    function dir();

    /**
     * Create new File Instance
     *
     * @return iFile
     */
    function file();

    /**
     * Create new Link Instance
     *
     * @return iLink
     */
    function link();

    /**
     * Create File Or Folder From Given Path
     *
     * - if not exists
     *   name without extension considered as folder
     *   else this is file
     * - if exists
     *   check type of current node and make object
     *
     * @param string $path Path
     *
     * @return mixed
     */
    function createFromPath($path);

    /**
     * Open Existence File Or Folder
     *
     * @param iCommon $node File/Folder
     *
     * @return iCommon|iFile|iLink
     */
    function open(iCommon $node);

    /**
     * Write File To Storage
     *
     * @param iCommon|iFile|iDirectory|iLink $node File
     *
     * @return $this
     */
    function write(iCommon $node);
}
