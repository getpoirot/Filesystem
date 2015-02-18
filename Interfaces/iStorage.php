<?php
namespace Poirot\Filesystem\Interfaces;

use Poirot\Filesystem\Interfaces\Filesystem\iCommon;
use Poirot\Filesystem\Interfaces\Filesystem\iDirectory;
use Poirot\Filesystem\Interfaces\Filesystem\iDirectoryInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iFile;
use Poirot\Filesystem\Interfaces\Filesystem\iLink;

/**
 * Storage can implement OptionsProviderInterface
 */
interface iStorage extends iFilesystemProvider
{
    const FS_TYPE_FILE    = 'file';
    const FS_TYPE_LINK    = 'link';
    const FS_TYPE_DIR     = 'dir';
    const FS_TYPE_UNKNOWN = 'unknown';

    /**
     * Set Basename of Storage
     *
     * @param string $name Basename
     *
     * @return $this
     */
    function setBasename($name);

    /**
     * Gets the name identifier of the storage
     *
     * ! the returned name should be the same for
     *   every storage object that is created with the same parameters
     *   and two storage objects with the same name should refer to two
     *   storages that display the same files.
     *
     * @return string
     */
    function getBasename();

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
     * Mount A Storage To Filesystem Directory
     *
     * @param iDirectoryInfo $dir
     *
     * @return $this
     */
    function mount(iDirectoryInfo $dir);

    /**
     * UnMount Mounted Storage
     *
     * @return $this
     */
    function unmount();

    /**
     * Write File To Storage
     *
     * ! check iCommon Object to match to
     *   class filesystem implementation or
     *   object type
     *
     * @param iCommon|iFile|iDirectory|iLink $node File
     *
     * @throws \Exception Throw Exception if file exists/fail write/unknown filesystem
     * @return $this
     */
    function write(iCommon $node);

    /**
     * List Contents
     *
     * - all fs nodes must have same Filesystem
     *   as storage Filesystem
     *   it means the Local Filesystem can't have
     *   a Directory with DropBox Filesystem
     *
     *   all AbstractNodeCommon extended are FilesystemAware
     *
     * @return array[iCommon]
     */
    function lsContents();

    /**
     * Get Filesystem node type
     *
     * FS_TYPE_FILE
     * FS_TYPE_LINK
     * FS_TYPE_DIR
     * ...
     *
     * @param iCommon $node
     *
     * @return string
     */
    function typeOf(/* iCommon */$node);
}
