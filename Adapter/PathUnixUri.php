<?php
namespace Poirot\Filesystem\Adapter;

use Poirot\Core\BuilderSetterTrait;
use Poirot\Filesystem\Interfaces\Filesystem\iFSPathUri;

class PathUnixUri implements iFSPathUri
{
    use BuilderSetterTrait {
        setupFromArray as fromArray;
    }

    protected $basename;
    protected $extension;
    protected $pathuri;

    protected $leadingDot = false;

    /**
     * Construct
     *
     * @param array|string $path ArraySetter or extracted info from path
     */
    function __construct($path = null)
    {
        if (is_string($path))
            $this->fromString($path);
        elseif (is_array($path))
            $this->fromArray($path);
    }

    /**
     * Build Object From String
     *
     * @param string $pathUri
     *
     * @return $this
     */
    function fromString($pathUri)
    {
        $path = [];
        if (is_string($pathUri))
            $path = $this->getPathInfo($pathUri);

        $this->fromArray($path);

        return $this;
    }

    /*function fromArray(array $path) {};*/ # Using Trait

    /**
     * Build Object From PathUri
     *
     * @param iFSPathUri $path
     *
     * @return $this
     */
    function fromPathUri(iFSPathUri $path)
    {
        $this->fromArray($path->toArray());

        return $this;
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

        return ($extension === '' || $extension === null)
            ? $filename
            : $filename.'.'.$extension;
    }

    /**
     * Set Path
     *
     * - in form of ['path', 'to', 'dir']
     *
     * @param array|string $path Path To File/Folder
     *
     * @throws \Exception
     * @return $this
     */
    function setPath($path)
    {
        if (is_string($path)) {
            $path = explode('/', $path);
            if ($path[0] == '.' && !$this->leadingDot)
                unset ($path[0]);
        }

        if (!is_array($path))
            throw new \Exception(sprintf(
                'Path Must Be Array Or String, "%s" given.'
                , is_object($path) ? get_class($path) : gettype($path)
            ));

        $this->pathuri = $path;

        return $this;
    }

    /**
     * Gets the path without filename
     *
     * - return in form of ['path', 'to', 'dir']
     *
     * @return array
     */
    function getPath()
    {
        return $this->pathuri;
    }

    /**
     * Get Imploded Path
     *
     * @return string
     */
    function getImPath()
    {
        return implode('/', $this->pathuri);
    }

    /**
     * Get Path Name To File Or Folder
     *
     * - include full path for remote files
     * - include extension for files
     *
     * @return string
     */
    function getRealPathName()
    {
        $filepath = implode('/', $this->getPath());
        if ($filepath === '/' || $filepath === '')
            $realpath = $filepath.$this->getFilename();
        else
            $realpath = $filepath.'/'.$this->getFilename();

        return $this->normalizePath($realpath);
    }

    /**
     * Get PathUri Object As String
     *
     * @return string
     */
    function toString()
    {
        return $this->getRealPathName();
    }

    /**
     * Get Array In Form Of PathInfo
     *
     * return [
     *  'path'      => ['path', 'to', 'dir'],
     *  'impath'    => 'path/to/dir',
     *  'basename'  => 'name_with', # without extension
     *  'extension' => 'ext',
     *  'filename'  => 'name_with.ext',
     * ]
     *
     * @return array
     */
    function toArray()
    {
        return [
            'path'      => $this->getPath(),
            'impath'    => $this->getImPath(),
            'basename'  => $this->getBasename(),
            'extension' => $this->getExtension(),
            'filename'  => $this->getFilename(),
        ];
    }

    /**
     * Extract Path Info
     *
     * @param string $path
     *
     * @return array
     */
    protected function getPathInfo($path)
    {
        $path = $this->normalizePath($path);

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

    /**
     * Fix common problems with a file path
     *
     * @param string $path
     * @param bool   $stripTrailingSlash
     *
     * @return string
     */
    protected function normalizePath($path, $stripTrailingSlash = true)
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
}
 