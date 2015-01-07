<?php
namespace Poirot\Filesystem;

class Util 
{
    /**
     * Fix common problems with a file path
     *
     * @param string $path
     * @param bool   $stripTrailingSlash
     *
     * @return string
     */
    public static function normalizePath($path, $stripTrailingSlash = true)
    {
        if ($path == '')
            return '/';

        // convert paths to portables one
        $path = str_replace('\\', '/', $path);

        // add leading slash
        if ($path[0] !== '/')
            $path = '/' . $path;

        // remove sequences of slashes
        $path = preg_replace('#/{2,}#', '/', $path);

        //remove trailing slash
        if ($stripTrailingSlash and strlen($path) > 1 and substr($path, -1, 1) === '/')
            $path = substr($path, 0, -1);

        return $path;
    }
}
 