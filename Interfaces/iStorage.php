<?php
namespace Poirot\Filesystem\Interfaces;

use Poirot\Filesystem\Interfaces\Filesystem\iCommon;

/**
 * Storage can implement OptionsProviderInterface
 */
interface iStorage extends iCommon
{
    const FS_TYPE_FILE    = 'file';
    const FS_TYPE_LINK    = 'link';
    const FS_TYPE_DIR     = 'dir';
    const FS_TYPE_STORAGE = 'storage';
    const FS_TYPE_UNKNOWN = 'unknown';

    /**
     * Gets the name identifier of the storage
     *
     * ! the returned name should be the same for every storage object that is created with the same parameters
     * and two storage objects with the same name should refer to two storages that display the same files.
     *
     * - it's common with directories interfaces
     *
     * @return string
     */
    function getIdentity();

    /**
     * Get Current Filesystem/Storage Working Directory
     *
     * - storage with empty or '/' working directory
     *   mean the base storage
     *
     * - with mounting child storage, cwd will append
     *
     * @return string
     */
    function getCwd();

    /**
     * List Contents
     *
     * - Must use createFromPath Method to create instance
     * - Must Display Mounted Storages
     *
     * @return array[iCommon|iStorage]
     */
    function lsContents();

    /**
     * Mount External Storage
     *
     * - The mounted directory will show on lsContent
     *
     * @param iStorage $storage
     *
     * @return $this
     */
    function mount(iStorage $storage);

    /**
     * UnMount Mounted Storage
     *
     * @param iStorage $storage
     *
     * @return $this
     */
    function unmount(iStorage $storage);

    /**
     * Create File Or Folder From Given Path
     * Path's is always /path/to/file_or_folder
     *
     * ! beware of mounted storages
     *
     * - if not exists
     *   name without extension considered as folder
     *   else this is file
     * - if exists
     *   check type of current node and make object
     *
     * @param string $path Path
     *
     * @return iCommon|false Return False If Not Found
     */
    function createFromPath($path);

    /**
     * Is Mounted Storage?
     *
     * @return bool
     */
    function isMount();

    /**
     * Write File To Storage
     *
     * ! check iCommon Object to match to
     *   class filesystem implementation or
     *   object type
     *
     * - with creating files or folder cwd will
     *   append as path
     *
     * @param iCommon|iFile|iDirectory|iLink $node File
     *
     * @throws \Exception Throw Exception if file exists/fail write/unknown filesystem
     * @return $this
     */
    function write(iCommon $node);

    /**
     * Get Filesystem node type
     *
     * FS_TYPE_FILE
     * FS_TYPE_LINK
     * FS_TYPE_DIR
     * ...
     *
     * @param iCommon|iStorage $node
     *
     * @return string
     */
    function typeOf($node);
}
