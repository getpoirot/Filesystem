<?php
namespace Poirot\Filesystem\Util;

use Poirot\Core\AbstractOptions;
use Poirot\Filesystem\Adapter\Directory;
use Poirot\Filesystem\Adapter\File;
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

    protected $stream_open_path;
    protected $stream_open_orgpath;
    protected $stream_open_mode;
    protected $stream_open_opts;
    protected $stream_read_position;

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
        if (empty($scheme) || !array_key_exists($scheme, self::$__wrappers))
            throw new \Exception(sprintf(
                'Invalid Wrapper "%s".'
                , $scheme
            ));

        $this->_onRegLabel = $scheme;

        $this->_filesystem = self::$__wrappers[$scheme];
    }

    // Implement Stream Wrapper:
    /**
     * Open file or URL.
     * This method is called immediately after the wrapper is initialized (f.e.
     * by fopen() and file_get_contents()).
     *
     * @param   string  $path           Specifies the URL that was passed to the
     *                                  original function.
     * @param   string  $mode           The mode used to open the file, as
     *                                  detailed for fopen().
     * @param   int     $options        Holds additional flags set by the
     *                                  streams API. It can hold one or more of
     *                                  the following values OR'd together:
     *                                    * STREAM_USE_PATH, if path is relative,
     *                                      search for the resource using the
     *                                      include_path;
     *                                    * STREAM_REPORT_ERRORS, if this is
     *                                    set, you are responsible for raising
     *                                    errors using trigger_error during
     *                                    opening the stream. If this is not
     *                                    set, you should not raise any errors.
     * @param   string  &$openedPath    If the $path is opened successfully, and
     *                                  STREAM_USE_PATH is set in $options,
     *                                  $openedPath should be set to the full
     *                                  path of the file/resource that was
     *                                  actually opened.
     * @return  bool
     */
    function stream_open($path, $mode, $options, &$opened_path)
    {
        try {
            $this->__initCurrFsFromPath($path);
        } catch (\Exception $e)
        {
            // Wrapper not supported.
            return false;
        }

        $cpath = $this->__cleanUpPath($path);

        $this->stream_open_orgpath = $path;
        $this->stream_open_path = $cpath;
        $this->stream_open_mode = $mode;
        $this->stream_open_opts = $options;

        if ($options & STREAM_USE_PATH)
            $opened_path = $path;

        return true;
    }

    /**
     * Close a resource.
     * This method is called in response to fclose().
     * All resources that were locked, or allocated, by the wrapper should be
     * released.
     *
     * @return  void
     */
    function stream_close()
    {
        $this->stream_open_path = null;
        $this->stream_open_mode = null;
        $this->stream_open_opts = null;

        $this->stream_read_position = 0;
    }

    /**
     * Write to stream.
     * This method is called in response to fwrite().
     *
     * @param string $data
     *
     * @return int
     */
    function stream_write($data)
    {
        if ($this->stream_open_path === null)
            return false;

        // TODO implement stream open mode
        // TODO implement stream aware filesystem

        $file = new File($this->stream_open_path);
        $this->_filesystem->putFileContents($file, $data);

        $size = $file->getSize();
        $this->stream_read_position += $size;

        return $size;
    }

    /**
     * Read from stream.
     * This method is called in response to fread() and fgets().
     *
     * @param   int     $count    How many bytes of data from the current
     *                            position should be returned.
     * @return  string
     */
    function stream_read($count)
    {
        if ($this->stream_open_path === null)
            return false;

        // TODO implement stream open mode
        // TODO implement stream aware filesystem

        $file    = $this->_filesystem->mkFromPath($this->stream_open_path);
        $content = $this->_filesystem->getFileContents($file);

        $this->stream_read_position += $file->getSize();

        return $content;
    }

    /**
     * Tests for end-of-file on a file pointer.
     * This method is called in response to feof().
     *
     * @return  bool
     */
    function stream_eof()
    {
        if ($this->stream_open_path === null)
            return true;

        // TODO implement stream open mode
        // TODO implement stream aware filesystem

        $file    = $this->_filesystem->mkFromPath($this->stream_open_path);

        return ($file->getSize() >= $this->stream_read_position);
    }

    /**
     * Retrieve information about a file resource.
     * This method is called in response to fstat()
     *
     * @return array
     */
    function stream_stat()
    {
        $return = $this->url_stat($this->stream_open_orgpath, 0);

        return $return;
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
        if ($this->_filesystem->isDir($path))
            trigger_error(sprintf(
                'mkdir(): File "%s" exists'
                , $path
            ), E_USER_WARNING);
        else {
            try {
                $this->_filesystem->mkDir(new Directory($path), new FilePermissions($mode));
            }
            catch (\Exception $e)
            {
                trigger_error(sprintf(
                    '%s'
                    , $e->getMessage()
                ), E_USER_WARNING);

                return false;
            }
        }

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

        if (!$this->_filesystem->isDir($path)) {
            trigger_error(sprintf(
                'opendir(%s): failed to open dir: No such file or directory'
                , $path
            ), E_USER_WARNING);

            return false;
        }

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

        try {
            $scDir = $this->_filesystem->scanDir(new Directory($this->dir_opendir_path));

            if (count($scDir) > $this->dir_opendir_counter) {
                $return = $scDir[$this->dir_opendir_counter];
                $this->dir_opendir_counter++;
            }
        } catch (\Exception $e)
        {
            // false returned
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
