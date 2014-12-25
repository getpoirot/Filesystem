<?php
namespace Poirot\Filesystem;

/**
 * Storages can implement OptionsProviderInterface
 */
interface iStorage extends \Iterator
{
    const PATH_SEPARATOR = '/';

    /**
     * List Contents
     *
     * @return array[iFile|iLink|iFolder]
     */
    function lsContent();

    /**
     * Write File To Storage
     *
     * @param iNode|iFile|iFolder|iLink $node File
     *
     * @return $this
     */
    function write(iNode $node);

    // Implement Iterator:

    /**
     * @return iFile|iFolder|iLink
     */
    function current();
}
