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
    protected $username;
    protected $password;

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
     */
    function setServerUri($serverUri)
    {
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
     * ! If Connection Available On Belong Filesystem
     *   on any related option changes refresh the connection
     *   on filesystem
     *
     * @param mixed $ftpFilesystem
     */
    function setFtpFilesystem(Filesystem $ftpFilesystem)
    {
        $this->ftpFilesystem = $ftpFilesystem;
    }
}
