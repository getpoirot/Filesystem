<?php
namespace Poirot\Filesystem\Interfaces\Filesystem;

interface iWritable
{
    /**
     * Make File/Folder if not exists
     *
     * @return bool
     */
    function mkIfNotExists();
}
