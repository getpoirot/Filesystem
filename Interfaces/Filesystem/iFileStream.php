<?php
namespace Poirot\Filesystem\Interfaces\Filesystem;

interface iFileStream extends iFileStreamInfo
{
    /**
     * Set Scheme/Protocol of File path
     *
     * @param string $scheme Protocol Scheme
     *
     * @return $this
     */
    function setScheme($scheme);

    /**
     * Get a file pointer resource on success
     *
     * - use fopen like func. to open stream resource
     *
     * @throw \Exception On Failure
     * @return Resource
     */
    function getResource();
}
