<?php
namespace Poirot\Filesystem\Util;

use Poirot\Core\AbstractOptions;
use Poirot\Filesystem\Interfaces\iFilesystem;
use Poirot\Stream\Wrapper\AbstractWrapper;

class FilesystemAsStreamWrapper extends AbstractWrapper
{
    /**
     * Construct
     *
     * @param iFilesystem $fs           Filesystem
     * @param string      $wrapperLabel Label Using On Wrapper, exp. [file]://, dropbox://
     */
    function __construct(iFilesystem $fs, $wrapperLabel)
    {

    }

    /**
     * Get Wrapper Protocol Label
     *
     * - used on register/unregister wrappers, ...
     *
     *   label://
     *   -----
     *
     * @return string
     */
    function getLabel()
    {
        // TODO: Implement getLabel() method.
    }
}
 