<?php
namespace Poirot\Filesystem\Util;

use Poirot\Filesystem\Interfaces\Filesystem\File\iFileContentDelivery;
use Poirot\Stream\Interfaces\iStreamCommon;
use Poirot\Stream\Streamable;
use Poirot\Stream\StreamClient;

class StreamContentDelivery implements iFileContentDelivery
{
    /**
     * @var StreamClient
     */
    protected $client;

    /**
     * Construct
     *
     * @param iStreamCommon $client
     *        - The WrapperClient Or StreamClient Can Be Used
     */
    function __construct(iStreamCommon $client)
    {
       $this->client = $client;
    }

    function __toString()
    {
        $streamResource = $this->client->getConnect();
        $streamable     = new Streamable($streamResource);

        return $streamable->read();
    }
}
