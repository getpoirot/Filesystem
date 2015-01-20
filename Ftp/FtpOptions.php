<?php
namespace Poirot\Filesystem\Ftp;

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
    protected $port = 21;
    protected $timeout = 90;

    protected $username;
    protected $password;

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

        $this->fromArray($options);

        return $this;
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
        if (strpos($serverUri, 'ftp://'))
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
}
