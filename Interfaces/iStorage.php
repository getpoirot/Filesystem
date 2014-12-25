<?php
namespace Poirot\Filesystem\Interfaces;

/**
 * Storages can implement OptionsProviderInterface
 */
interface iStorage extends \IteratorAggregate
{
    const DS = DIRECTORY_SEPARATOR;

    /**
     * Current Working Directory
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
     * List Contents
     *
     * @return array[iFile|iLink|iFolder]
     */
    function lsContent();

    /**
     * Create new Folder Instance
     *
     * @return iFolder
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
     * @param iNode $node File/Folder
     *
     * @return iNode|iFile|iLink
     */
    function open(iNode $node);

    /**
     * Write File To Storage
     *
     * @param iNode|iFile|iFolder|iLink $node File
     *
     * @return $this
     */
    function write(iNode $node);
}
