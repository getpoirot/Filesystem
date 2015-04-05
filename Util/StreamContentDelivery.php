<?php
namespace Poirot\Filesystem\Util;

use Poirot\Filesystem\Interfaces\Filesystem\File\iFileContentDelivery;
use Poirot\Stream\Interfaces\iSResource;
use Poirot\Stream\Interfaces\iStreamable;
use Poirot\Stream\Interfaces\iStreamCommon;
use Poirot\Stream\Streamable;
use Poirot\Stream\StreamClient;

/**
 * @method $this setResource(iSResource $handle)
 * @method iSResource getResource()
 * @method $this pipeTo(iStreamable $destStream, $maxByte = null, $offset = 0)
 * @method string read($inByte = null)
 * @method string readLine($ending = "\n", $inByte = null)
 * @method $this write($content, $inByte = null)
 * @method $this sendData($data, $flags = null)
 * @method string receiveFrom($maxByte, $flags = STREAM_OOB)
 * @method int getTransCount()
 * @method $this seek($offset, $whence = SEEK_SET)
 * @method $this rewind()
 */
class StreamContentDelivery
    implements
    iFileContentDelivery
{
    /**
     * @var StreamClient
     */
    protected $client;

    /**
     * @var iStreamable
     */
    protected $__streamable;

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
        return $this->read();
    }

    /**
     * Proxy call to streamable
     *
     * @param $method
     * @param $arguments
     *
     * @return mixed
     */
    function __call($method, $arguments)
    {
        $streamable = $this->__getStreamable();

        return call_user_func_array([$streamable, $method], $arguments);
    }

    protected function __getStreamable()
    {
        if (!$this->__streamable) {
            $streamResource = $this->client->getConnect();
            $this->__streamable = new Streamable($streamResource);
        }

        return $this->__streamable;
    }
}
