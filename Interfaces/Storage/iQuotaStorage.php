<?php
namespace Poirot\Filesystem\Interfaces\Storage;

interface iQuotaStorage
{
    /**
     * Set Quota
     *
     * @param int $bytes Quota in bytes
     *
     * @return $this
     */
    function setQuota($bytes);

    /**
     * Get Total Quota in bytes
     *
     * @return int
     */
    function getQuota();

    /**
     * Get Available Free Space in bytes
     *
     * @return int
     */
    function getFreeSpace();
}
