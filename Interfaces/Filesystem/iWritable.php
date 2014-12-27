<?php
namespace Poirot\Filesystem\Interfaces;

interface iWritable
{
    /**
     * Make File/Folder if not exists
     *
     * @return bool
     */
    function mkIfNotExists();
}
