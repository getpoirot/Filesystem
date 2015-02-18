<?php
namespace Poirot\Filesystem\Adapter;

use Poirot\Core\BuilderSetterTrait;
use Poirot\Filesystem\Interfaces\Filesystem\iFSPathUri;

class NodePathUri implements iFSPathUri
{
    use BuilderSetterTrait;

    protected $basename;
    protected $extension;
    protected $pathuri;

    protected $leadingDot = false;

    /**
     * Construct
     *
     * @param array|string $setterBuilder ArraySetter or extracted info from path
     */
    function __construct($setterBuilder = null)
    {
        if (is_string($setterBuilder))
            $setterBuilder = self::getPathInfo(
                self::normalizePath($setterBuilder)
            );

        if (is_array($setterBuilder))
            $this->setupFromArray($setterBuilder);
    }

    /**
     * Set Filename of file or folder
     *
     * ! without extension
     *
     * - /path/to/filename[.ext]
     * - /path/to/folderName/
     *
     * @param string $name Basename
     *
     * @return $this
     */
    function setBasename($name)
    {
        $this->basename = (string) $name;

        return $this;
    }

    /**
     * Gets the file name of the file
     *
     * - Without extension on files
     *
     * @return string
     */
    function getBasename()
    {
        return $this->basename;
    }

    /**
     * Set the file extension
     *
     * ! throw exception if file is lock
     *
     * @param string|null $ext File Extension
     *
     * @return $this
     */
    function setExtension($ext)
    {
        $this->extension = $ext;

        return $this;
    }

    /**
     * Gets the file extension
     *
     * @return string
     */
    function getExtension()
    {
        return $this->extension;
    }

    /**
     * Get Filename Include File Extension
     *
     * ! It's a combination of basename+'.'.extension
     *   combined with a dot
     *
     * @return string
     */
    function getFilename()
    {
        $filename  = $this->getBasename();
        $extension = $this->getExtension();

        return ($extension === '' || $extension === null) ? $filename
            : $filename.'.'.$extension;
    }

    /**
     * Set Path
     *
     * @param string|null $path Path To File/Folder
     *
     * @return $this
     */
    function setPath($path)
    {
        $this->pathuri = (string) $path;

        return $this;
    }

    /**
     * Gets the path without filename
     *
     * @return string
     */
    function getPath()
    {
        $path = $this->pathuri;
        $path = explode('/', $path);
        if ($path[0] == '.' && !$this->leadingDot)
            unset ($path[0]);

        return implode('/', $path);
    }

    /**
     * Path Uri Include ./
     *
     * @return $this
     */
    function withLeadingDot()
    {
        $this->leadingDot = true;

        return $this;
    }

    /**
     * Strip ./ from paths
     *
     * @return $this
     */
    function withoutLeadingDot()
    {
        $this->leadingDot = false;

        return $this;
    }

    /**
     * Get Path Name To File Or Folder
     *
     * - include full path for remote files
     * - include extension for files
     * - usually use Util::normalizePath on return
     *
     * @return string
     */
    function getRealPathName()
    {
        $filepath = $this->getPath();
        if ($filepath === '/' || $filepath === '')
            $realpath = $filepath.$this->getFilename();
        else
            $realpath = $filepath.'/'.$this->getFilename();

        return self::normalizePath($realpath);
    }

    /**
     * Alias of getRealPathName
     *
     * @return string
     */
    function toString()
    {
        return $this->getRealPathName();
    }

    /**
     * Set Scheme/Protocol of File path
     *
     * @param string $scheme Protocol Scheme
     *
     * @return $this
     */
    function setScheme($scheme)
    {
        // TODO: Implement setScheme() method.
    }

    /**
     * Get Scheme Protocol part of file path
     *
     * @return string
     */
    function getScheme()
    {
        // TODO: Implement getScheme() method.
    }

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
            return '.';

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
        $m    = pathinfo($path);
        (!isset($m['dirname']))   ?: $ret['path']      = $m['dirname'];  // For file with name.ext
        (!isset($m['basename']))  ?: $ret['filename']  = $m['basename']; // <= name.ext
        (!isset($m['filename']))  ?: $ret['basename']  = $m['filename']; // <= name
        (!isset($m['extension'])) ?: $ret['extension'] = $m['extension'];

        if (isset($ret['extension']) && $ret['filename'] === '') {
            // for folders similar to .ssh
            unset($ret['extension']);

            $ret['filename'] = $ret['basename'];
        }

        if ($ret['path'] === '')
            unset($ret['path']);

        return $ret;
    }
}
 