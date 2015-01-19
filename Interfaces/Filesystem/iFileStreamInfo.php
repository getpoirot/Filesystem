<?php
namespace Poirot\Filesystem\Interfaces\Filesystem;

interface iFileStreamInfo 
{
    /**
     * Get Scheme Protocol part of file path
     *
     * @return string
     */
    function getScheme();
}
