<?php
namespace Poirot\Filesystem\Interfaces\Filesystem\File;

/**
 * It uses to implement content delivery fo reading
 * large files content, generating fake content
 * or storing content of files on memory in lazy mode
 * to reduce memory size on contents
 */
interface iFileContentDelivery 
{
    function __toString();
}
