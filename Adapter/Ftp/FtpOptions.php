<?php
namespace Poirot\Filesystem\Adapter\Ftp;

use Poirot\Core\AbstractOptions;

class FtpOptions extends AbstractOptions
{
    /**
     * ! injected filesystem to refresh connection
     *   on belonged options changed
     *
     * @var Filesystem
     */
    protected $ftpFilesystem;

    protected $serverUri;
    protected $port    = 21;
    protected $timeout = 90;
    protected $useSsl;

    protected $username = 'anonymous';
    protected $password = '';

    /**
     * Construct
     *
     * @param array|string|null $options
     */
    function __construct($options = null)
    {
        if (is_string($options) && strpos($options, 'ftp://') === 0)
            $options = $this->extractOptions($options);

        if ($options !== null)
            parent::__construct($options);
    }

    /**
     * Set Options From Uri
     *
     * @param string $uri
     *
     * @throws \Exception
     * @return $this
     */
    function fromUri($uri)
    {
        $options = $this->extractOptions($uri);

        $this->fromArray($options);

        return $this;
    }

    /**
     * Set Options From Same Option Object
     *
     * - check for private and write_only methods
     *   to fully options copied to source option
     *   class object
     *
     * @param FtpOptions $options Options Object
     *
     * @throws \Exception
     * @return $this
     */
    function fromOption(/*FtpOptions*/ $options) // php is a donkey, why strict_error
    {                                            // when FtpOptions is extended AbstractOptions
        parent::fromOption($options);

        return $this;
    }

        protected function extractOptions($uri)
        {
            // Split FTP URI into:
            // $match[0] = ftp://username:password@sld.domain.tld/path1/path2/
            // $match[1] = ftp://
            // $match[2] = username
            // $match[3] = password
            // $match[4] = sld.domain.tld
            // $match[5] = /path1/path2/
            preg_match("/ftp:\/\/(.*?):(.*?)@(.*?)(\/.*)/i", $uri, $match);

            $options = [];
            $options['username']   = $match[2];
            $options['password']   = $match[3];
            $options['server_uri'] = $match[4];

            return $options;
        }

    /**
     * @return mixed
     */
    function getServerUri()
    {
        return $this->serverUri;
    }

    /**
     * ! Refresh FTP Connection
     *
     * @param mixed $serverUri
     * @throws \Exception
     */
    function setServerUri($serverUri)
    {
        $serverUri = strtolower($serverUri);
        if (strpos($serverUri, 'ftp://') === 0)
            throw new \Exception(
                "The FTP server address shouldn't have any trailing slashes
                and shouldn't be prefixed with ftp://
            ");

        $this->serverUri = $serverUri;

        (!$this->ftpFilesystem) ?: $this->ftpFilesystem->refreshResource = true;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param mixed $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return mixed
     */
    function getUsername()
    {
        return $this->username;
    }

    /**
     * ! Refresh FTP Connection
     *
     * @param mixed $username
     */
    function setUsername($username)
    {
        $this->username = $username;

        (!$this->ftpFilesystem) ?: $this->ftpFilesystem->refreshResource = true;
    }

    /**
     * @return mixed
     */
    function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * ! If Connection has made On injected Filesystem
     *   with any related option changes refresh the connection
     *
     * @param mixed $ftpFilesystem
     */
    function setFtpFilesystem(Filesystem $ftpFilesystem)
    {
        $this->ftpFilesystem = $ftpFilesystem;
    }

    /**
     * @return mixed
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param mixed $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @return mixed
     */
    public function getUseSsl()
    {
        return $this->useSsl;
    }

    /**
     * @param mixed $useSsl
     * @throws \Exception
     */
    public function setUseSsl($useSsl)
    {
        if ($useSsl) {
            // check environment for ssl support
            if (!function_exists('ftp_ssl_connect'))
                throw new \Exception("
                    SSL FTP is only available if both the ftp module and the OpenSSL support
                    is built statically into php, this means that on Windows this function will
                    be undefined in the official PHP builds. To make this function available
                    on Windows you must compile your own PHP binaries.
                ");
        }

        $this->useSsl = $useSsl;
    }
}
