<?php
namespace Poirot\Filesystem\Interfaces\Filesystem;

/**
 * Represent Full protocol://Path/uri/to/filename.ext
 *
 */
interface iFSStreamPathUri extends iFSPathUri
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
     * Get Scheme Protocol part of file path
     *
     * @return string
     */
    function getScheme();
}
