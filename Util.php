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

        // remove sequences of slashes
        $path = preg_replace('#/{2,}#', '/', $path);

        //remove trailing slash
        if ($stripTrailingSlash and strlen($path) > 1 and substr($path, -1, 1) === '/')
            $path = substr($path, 0, -1);

        return $path;
    }

    /**
     * Extract Path Info
     *
     * @param string $path
     *
     * @return array
     */
    public static function getPathInfo($path)
    {
        $path = self::normalizePath($path);

        $ret  = [];
        preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $path, $m);
        (!isset($m[1])) ?: $ret['path']      = $m[1];
        (!isset($m[2])) ?: $ret['basename']  = $m[2];
        (!isset($m[3])) ?: $ret['filename']  = $m[3];
        (!isset($m[5])) ?: $ret['extension'] = $m[5];

        if (isset($ret['extension']) && $ret['filename'] === '') {
            // for folders similar to .ssh
            unset($ret['extension']);

            $ret['filename'] = $ret['basename'];
        }

        if ($ret['path'] === '')
            unset($ret['path']);

        return $ret;
    }

    /**
     * For Directories we don't have extension
     * so a directory with name "jquery.slide"
     * have not extension, all consumed as filename
     *
     * @param $path
     *
     * @return array
     */
    public static function getDirPathInfo($path)
    {
        $ret = self::getPathInfo($path);
        if (isset($ret['extension'])) {
            $ret['filename'] .= '.'. $ret['extension'];

            unset($ret['extension']);
        }

        return $ret;
    }

    /**
     * Generate Safe Web Name From Filename
     *
     * @param string $fileName
     *
     * @return string
     */
    public static function makeSafe($fileName)
    {
        $regex = array('#(\.){2,}#', '#[^A-Za-z0-9\.\_\-]#', '#^\.#');

        return preg_replace($regex, '', $fileName);
    }
}
