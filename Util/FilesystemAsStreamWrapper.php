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
    /*
     * For stat() mode bits:
     * ! to detect is_dir, is_file, ....
     *
     * if ($fstats[mode] & 040000)
     * ... this must be a directory
     *
     * @see http://www.manpagez.com/man/2/stat/
     */
    const  S_IFMT  = 0170000;  /* type of file */
    const  S_IFDIR = 0040000;  /* directory */
    const  S_IFREG = 0100000;  /* regular */
    const  S_IFLNK = 0120000;  /* symbolic link */

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

    protected $stream_write_buff;

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
        $this->stream_open_orgpath = null;
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

        $this->stream_write_buff = $data;
        $size = mb_strlen($data, '8bit');

        try{
            $file = new File($this->stream_open_path);
            $this->_filesystem->putFileContents($file, $this->stream_write_buff);

            $this->stream_write_buff = null;
        } catch (\Exception $e)
        {
            return false;
        }

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
        if ($this->stream_eof() || $this->stream_open_path === null)
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

        return ($this->stream_read_position >= $file->getSize());
    }

    /**
     * Seek to specific location in a stream.
     * This method is called in response to fseek().
     * The read/write position of the stream should be updated according to the
     * $offset and $whence.
     *
     * @param   int     $offset    The stream offset to seek to.
     * @param   int     $whence    Possible values:
     *                               * SEEK_SET to set position equal to $offset
     *                                 bytes ;
     *                               * SEEK_CUR to set position to current
     *                                 location plus $offsete ;
     *                               * SEEK_END to set position to end-of-file
     *                                 plus $offset.
     * @return  bool
     */
    function stream_seek($offset, $whence = SEEK_SET )
    {
        kd(__FUNCTION__);
    }

    /**
     * Retrieve the current position of a stream.
     * This method is called in response to ftell()
     *
     * @return int
     */
    function stream_tell()
    {
        kd(__FUNCTION__);
    }

    /**
     * Flush the output.
     * This method is called in response to fflush().
     * If we have cached data in our stream but not yet stored it into the
     * underlying storage, we should do so now.
     *
     * @return  bool
     */
    function stream_flush()
    {
        if ($this->stream_write_buff !== null)
            $this->stream_write($this->stream_write_buff);

        return true;
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

    /**
     * @param string  $path
     * @param int     $option
     * @param mixed   $value
     *
     * @return bool
     */
    function stream_metadata($path, $option, $value )
    {
        kd(__FUNCTION__);
    }

    /**
     * Truncate a stream to a given length.
     *
     * @param int $new_size
     *
     * @return bool
     */
    function stream_truncate($new_size)
    {
        kd(__FUNCTION__);
    }

    /**
     * Advisory file locking.
     * This method is called in response to flock(), when file_put_contents()
     * (when flags contains LOCK_EX), stream_set_blocking() and when closing the
     * stream (LOCK_UN).
     *
     * @param   int     $operation    Operation is one the following:
     *                                  * LOCK_SH to acquire a shared lock (reader) ;
     *                                  * LOCK_EX to acquire an exclusive lock (writer) ;
     *                                  * LOCK_UN to release a lock (shared or exclusive) ;
     *                                  * LOCK_NB if we don't want flock() to
     *                                    block while locking (not supported on
     *                                    Windows).
     * @return  bool
     */
    function stream_lock($operation)
    {
        kd(__FUNCTION__);
    }

    /**
     * Change stream options.
     * This method is called to set options on the stream.
     *
     * @param   int     $option    One of:
     *                               * STREAM_OPTION_BLOCKING, the method was
     *                                 called in response to
     *                                 stream_set_blocking() ;
     *                               * STREAM_OPTION_READ_TIMEOUT, the method
     *                                 was called in response to
     *                                 stream_set_timeout() ;
     *                               * STREAM_OPTION_WRITE_BUFFER, the method
     *                                 was called in response to
     *                                 stream_set_write_buffer().
     * @param   int     $arg1      If $option is:
     *                               * STREAM_OPTION_BLOCKING: requested blocking
     *                                 mode (1 meaning block, 0 not blocking) ;
     *                               * STREAM_OPTION_READ_TIMEOUT: the timeout
     *                                 in seconds ;
     *                               * STREAM_OPTION_WRITE_BUFFER: buffer mode
     *                                 (STREAM_BUFFER_NONE or
     *                                 STREAM_BUFFER_FULL).
     * @param   int     $arg2      If $option is:
     *                               * STREAM_OPTION_BLOCKING: this option is
     *                                 not set ;
     *                               * STREAM_OPTION_READ_TIMEOUT: the timeout
     *                                 in microseconds ;
     *                               * STREAM_OPTION_WRITE_BUFFER: the requested
     *                                 buffer size.
     * @return  bool
     */
    function stream_set_option($option, $arg1, $arg2)
    {
        kd(__FUNCTION__);
    }

    /**
     * Retrieve the underlaying resource.
     *
     * @param   int     $castAs    Can be STREAM_CAST_FOR_SELECT when
     *                             stream_select() is calling stream_cast() or
     *                             STREAM_CAST_AS_STREAM when stream_cast() is
     *                             called for other uses.
     * @return  resource
     */
    function stream_cast($cast_as)
    {
        kd(__FUNCTION__);
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

        if (is_callable([$this->_filesystem, 'getStat'])) {
            return $this->_filesystem->getStat($source);
        }

        $mode = ($this->_filesystem->isDir($source))
            // dir
            ? self::S_IFDIR
            : (
                ($this->_filesystem->isLink($source))
                // link
                ? self::S_IFLNK
                :(
                    ($this->_filesystem->isFile($source))
                    // file
                    ? self::S_IFREG
                    // unknown
                    : false
                )
            );

        $retArr = [
            # device number
            0     => null,
            'dev' => null,
            # inode number *
            1     => null,
            'ino' => null,
            # inode protection mode
            2      => $mode,
            'mode' => $mode,
            # number of links
            3       => null,
            'nlink' => null,
            # userid of owner *
            4     => $source->getOwner(),
            'uid' => $source->getOwner(),
            # groupid of owner *
            5     => $source->getGroup(),
            'gid' => $source->getGroup(),
            # device type, if inode device
            6      => null,
            'rdev' => null,
            # size in bytes
            7      => ($this->_filesystem->isFile($source))  ? $source->getSize()  : 0,
            'size' => ($this->_filesystem->isFile($source))  ? $source->getSize()  : 0,
            # time of last access (Unix timestamp)
            8       => $source->getATime(),
            'atime' => $source->getATime(),
            # time of last modification (Unix timestamp)
            9       => $source->getMTime(),
            'mtime' => $source->getMTime(),
            # time of last inode change (Unix timestamp)
            10      => $source->getCTime(),
            'ctime' => $source->getCTime(),
            # blocksize of filesystem IO **
            11        => -1,
            'blksize' => -1,
            # number of 512-byte blocks allocated **
            12       => 0,
            'blocks' => 0,
        ];

        return $retArr;
    }
}
