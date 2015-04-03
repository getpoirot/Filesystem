<?php
namespace Poirot\Filesystem\Util;

use Poirot\Core\AbstractOptions;
use Poirot\Filesystem\Adapter\Directory;
use Poirot\Filesystem\FilePermissions;
use Poirot\Filesystem\Interfaces\Filesystem\iLink;
use Poirot\Filesystem\Interfaces\iFilesystem;
use Poirot\Filesystem\Util;
use Poirot\Stream\Wrapper\AbstractWrapper;

class FilesystemAsStreamWrapper extends AbstractWrapper
{
    /**
     * @var array[iFilesystem]
     */
    protected static $__wrappers = [];

    /**
     * @var string
     */
    protected $_onRegLabel;

    /**
     * @var iFilesystem
     */
    protected $_filesystem;

    /**
     * @var string R/W Mode
     */
    protected $_currMode;

    /**
     * @var array
     */
    protected $_currOptions;

    /**
     * @var string Current Open Directory Path
     * @see dir_opendir()
     */
    protected $dir_opendir_path;
    protected $dir_opendir_counter = 0; // using as pointer

    /**
     * Construct
     *
     * ! Arguments become null because on fopen this class constructed with
     *   no argument
     *
     * @param iFilesystem $fs           Filesystem
     * @param string      $wrapperLabel Label Using On Wrapper, exp. [file]://, dropbox://
     */
    function __construct(iFilesystem $fs = null, $wrapperLabel = null)
    {
        $this->_onRegLabel = $wrapperLabel;

        self::$__wrappers[$wrapperLabel] = $fs;
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
        return $this->_onRegLabel;
    }

    /**
     * Remove Wrapper Scheme From Path
     *
     * ! remove iacc://path/to/ from begining path
     *   to avoid recursive wrapper call and faild
     *   on using filesystem functions
     *
     * @param string $path
     *
     * @throws \Exception
     * @return string
     */
    protected function __cleanUpPath($path)
    {
        $scheme = $this->_onRegLabel;

        return str_replace($scheme.'://', '', $path);
    }

    /**
     * Initialize Current Filesystem Wrapper From Path Scheme
     *
     * @param string $path
     *
     * @throws \Exception
     */
    protected function __initCurrFsFromPath($path)
    {
        // Set Current Wrapper Label:
        $scheme = Util::urlParse($path, PHP_URL_SCHEME);
        if (!array_key_exists($scheme, self::$__wrappers))
            throw new \Exception(sprintf(
                'Invalid Wrapper "%s".'
                , $scheme
            ));

        $this->_onRegLabel = $scheme;

        $this->_filesystem = self::$__wrappers[$scheme];
    }


    // Implement Directories:

    /**
     * Create a directory.
     * This method is called in response to mkdir().
     *
     * @param   string  $path       Directory which should be created.
     * @param   int     $mode       The value passed to mkdir().
     * @param   int     $options    A bitwise mask of values.
     * @return  bool
     */
    function mkdir($path, $mode, $options)
    {
        $this->__initCurrFsFromPath($path);

        $path = $this->__cleanUpPath($path);
        $this->_filesystem->mkDir(new Directory($path), new FilePermissions($mode));

        return true;
    }

    /**
     * Remove a directory.
     * This method is called in response to rmdir().
     *
     * @param   string  $path       The directory URL which should be removed.
     * @param   int     $options    A bitwise mask of values.
     * @return  bool
     */
    function rmdir($path, $options)
    {
        $this->__initCurrFsFromPath($path);

        $path = $this->__cleanUpPath($path);
        $this->_filesystem->rmDir(new Directory($path));

        return true;
    }

    /**
     * Open directory handle.
     * This method is called in response to opendir().
     *
     * @param   string  $path       Specifies the URL that was passed to opendir().
     * @param   int     $options    Whether or not to enforce safe_mode (0x04).
     * @return  bool
     */
    function dir_opendir($path, $options)
    {
        $this->__initCurrFsFromPath($path);
        $path = $this->__cleanUpPath($path);

        if (!$this->_filesystem->isDir($path))
            return false;

        $this->dir_opendir_path    = $path;
        $this->dir_opendir_counter = 0;

        return true;
    }

    /**
     * Read entry from directory handle.
     * This method is called in response to readdir().
     *
     * @throws \Exception
     * @return mixed
     */
    function dir_readdir()
    {
        if ($this->dir_opendir_path === null)
            // No Directory Open
            return false;

        $return = false;

        $scDir = $this->_filesystem->scanDir(new Directory($this->dir_opendir_path));
        if (count($scDir) > $this->dir_opendir_counter) {
            $return = $scDir[$this->dir_opendir_counter];
            $this->dir_opendir_counter++;
        }

        return $return;
    }

    /**
     * Rewind directory handle.
     * This method is called in response to rewinddir().
     * Should reset the output generated by self::dir_readdir, i.e. the next
     * call to self::dir_readdir should return the first entry in the location
     * returned by self::dir_opendir.
     *
     * @return bool
     */
    function dir_rewinddir()
    {
        if ($this->dir_opendir_path === null)
            // No Directory Open
            return false;

        $this->dir_opendir_counter = 0;

        return true;
    }

    /**
     * Close directory handle.
     * This method is called in to closedir().
     * Any resources which were locked, or allocated, during opening and use of
     * the directory stream should be released.
     *
     * @return bool
     */
    function dir_closedir()
    {
        if ($this->dir_opendir_path === null)
            // No Directory Open
            return false;

        $this->dir_opendir_path = null;
        $this->dir_opendir_counter = 0;

        return true;
    }

    // Implement Filesystem:

    /**
     * Rename a file or directory.
     * This method is called in response to rename().
     * Should attempt to rename $from to $to.
     *
     * @param string $path_from The URL to current file.
     * @param string $path_to   The URL which $from should be renamed to.
     *
     * @return bool
     */
    function rename($path_from, $path_to)
    {
        $this->__initCurrFsFromPath($path_from);

        $path_from = $this->__cleanUpPath($path_from);
        $source    = $this->_filesystem->mkFromPath($path_from);

        $this->_filesystem->rename($source, $this->__cleanUpPath($path_to));

        return true;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    function unlink($path)
    {
        $this->__initCurrFsFromPath($path);

        $path = $this->__cleanUpPath($path);

        try {
            $source = $this->_filesystem->mkFromPath($path);
            $this->_filesystem->unlink($source);
        } catch (\Exception $e)
        {
            // Todo Rise User Warning Error ...
            return false;
        }

        return true;
    }

    /**
     * Retrieve information about a file.
     * This method is called in response to all stat() related functions.
     *
     * @param   string  $path     The file URL which should be retrieve
     *                            information about.
     * @param   int     $flags    Holds additional flags set by the streams API.
     *                            It can hold one or more of the following
     *                            values OR'd together.
     *                            STREAM_URL_STAT_LINK: for resource with the
     *                            ability to link to other resource (such as an
     *                            HTTP location: forward, or a filesystem
     *                            symlink). This flag specified that only
     *                            information about the link itself should be
     *                            returned, not the resource pointed to by the
     *                            link. This flag is set in response to calls to
     *                            lstat(), is_link(), or filetype().
     *                            STREAM_URL_STAT_QUIET: if this flag is set,
     *                            our wrapper should not raise any errors. If
     *                            this flag is not set, we are responsible for
     *                            reporting errors using the trigger_error()
     *                            function during stating of the path.
     * @return  array
     */
    function url_stat($path, $flags)
    {
        $this->__initCurrFsFromPath($path);

        $path   = $this->__cleanUpPath($path);
        try {
            $source = $this->_filesystem->mkFromPath($path);
        } catch (\Exception $e)
        {
            return false;
        }

        if ($this->_filesystem->isLink($source)) {
            /** @var iLink $source */
            if ($flags & STREAM_URL_STAT_LINK == $flags)
                $source = $source->getTarget();
        }

        $retArr = [
            'dev' => null, # device number
            'ino' => null, # inode number *
            'mode' => null, # inode protection mode
            'nlink' => null, # number of links
            'uid' => $source->getOwner(), # userid of owner *
            'gid' => $source->getGroup(), # groupid of owner *
            'rdev' => null, # device type, if inode device
            'size' => ($this->_filesystem->isFile($source))  ? $source->getSize()  : 0, # size in bytes
            'atime' => ($this->_filesystem->isFile($source)) ? $source->getATime() : 0, # time of last access (Unix timestamp)
            'mtime' => ($this->_filesystem->isFile($source)) ? $source->getMTime() : 0, # time of last modification (Unix timestamp)
            'ctime' => ($this->_filesystem->isFile($source)) ? $source->getCTime() : 0, # time of last inode change (Unix timestamp)
            'blksize' => -1, # blocksize of filesystem IO **
            'blocks' => 0,   # number of 512-byte blocks allocated **
        ];

        return $retArr;
    }
}
