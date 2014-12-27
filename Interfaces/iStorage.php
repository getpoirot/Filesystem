<?php
namespace Poirot\Filesystem\Interfaces;

/**
 * Storage can implement OptionsProviderInterface
 */
interface iStorage
{
    const FS_TYPE_FILE      = 'file';
    const FS_TYPE_LINK      = 'link';
    const FS_TYPE_DIRECTORY = 'dir';

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
     * @param iStorage $directory
     *
     * @return $this
     */
    function mount(iStorage $directory);

    /**
     * UnMount Mounted Directory
     *
     * @param iStorage $directory
     *
     * @return $this
     */
    function unmount(iStorage $directory);

    /**
     * Is Mounted Storage?
     *
     * @return bool
     */
    function isMount();

    /**
     * Write File To Storage
     *
     * @param iCommon|iFile|iDirectory|iLink $node File
     *
     * @throws \Exception Throw Exception if file exists/fail write
     * @return $this
     */
    function write(iCommon $node);

    /**
     * List Contents
     *
     * - Must use createFromPath Method
     *
     * @return array[iFile|iLink|iDirectory]
     */
    function lsContent();

    /**
     * Create File Or Folder From Given Path
     * Path's is always /path/to/file_or_folder
     *
     * - if not exists
     *   name without extension considered as folder
     *   else this is file
     * - if exists
     *   check type of current node and make object
     *
     * @param string $path Path
     *
     * @throws \Exception Throw Exception if file not found
     * @return mixed
     */
    function createFromPath($path);

    /**
     * Get Filesystem node type
     *
     * FS_TYPE_FILE
     * FS_TYPE_LINK
     * FS_TYPE_DIRECTORY
     *
     * @param iCommon $node
     *
     * @return string
     */
    function typeOf(iCommon $node);
}
