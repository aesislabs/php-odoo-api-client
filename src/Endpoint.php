<?php

namespace Aesislabs\Component\Odoo;

use Aesislabs\Component\Odoo\Exception\RemoteException;
use Aesislabs\Component\Odoo\Exception\RequestException;
use Laminas\XmlRpc\Client as XmlRpcClient;
use Aesislabs\Component\XmlRpc\Exception\RemoteException as XmlRpcRemoteException;
use Throwable;

class Endpoint
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var XmlRpcClient
     */
    private $client;

    public function __construct(string $url)
    {
        $this->url = $url;
        $this->client = new XmlRpcClient($url);
        $this->client->getHttpClient()->setOptions(['timeout' => 60]);
    }

    /**
     * @throws RequestException when request failed
     *
     * @return mixed
     */
    public function call(string $method, array $args = [])
    {
        try {
            return $this->client->call($method, $args);
        } catch (XmlRpcRemoteException $exception) {
            if (preg_match('#cannot marshal None unless allow_none is enabled#', $exception->getMessage())) {
                return null;
            }

            throw RemoteException::create($exception);
        } catch (Throwable $exception) {
            throw new RequestException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getClient(): XmlRpcClient
    {
        return $this->client;
    }
}
