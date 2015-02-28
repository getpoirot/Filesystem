<?php
namespace Poirot\Filesystem\Interfaces;

use Poirot\PathUri\Interfaces\iPathJoinedUri;

interface iIsolatedFS extends iFilesystem
{
    /**
     * Changes Root Directory Path
     *
     * - root directory must be absolute
     *
     * @param string|iPathJoinedUri $dir
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function chRootPath($dir);

    /**
     * Get Root Directory Path
     *
     * @return iPathJoinedUri
     */
    function getRootPath();
}
