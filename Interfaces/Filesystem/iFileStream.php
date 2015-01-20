<?php
namespace Poirot\Filesystem\Interfaces\Filesystem;

interface iFileStream extends iFileStreamInfo
{
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
