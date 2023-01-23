<?php

namespace Papertrail\Service;

/**
 * Abstract base class for all services.
 */
abstract class AbstractService
{

    protected $client;

    protected $streamingClient;

    /**
     * Initializes a new instance of the {@link AbstractService} class.
     *
     * @param \Stripe\StripeClientInterface $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Gets the client used by this service to send requests.
     *
     * @return \Stripe\StripeClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Gets the client used by this service to send requests.
     *
     * @return \Stripe\StripeStreamingClientInterface
     */
    public function getStreamingClient()
    {
        return $this->streamingClient;
    }

    protected function request($method, $path, $params, $opts)
    {
        $request =  $this->getClient()->request($method, $path, $params, $opts);
        return json_decode($request->getBody());
    }

    protected function requestStream($method, $path, $readBodyChunkCallable, $params, $opts)
    {
        return $this->getStreamingClient()->requestStream($method, $path, $readBodyChunkCallable, static::formatParams($params), $opts);
    }

    protected function requestCollection($method, $path, $params, $opts)
    {
        return $this->getClient()->requestCollection($method, $path, static::formatParams($params), $opts);
    }

    protected function requestSearchResult($method, $path, $params, $opts)
    {
        return $this->getClient()->requestSearchResult($method, $path, static::formatParams($params), $opts);
    }

    protected function buildPath($basePath, ...$ids)
    {
        foreach ($ids as $id) {
            if (null === $id || '' === \trim($id)) {
                $msg = 'The resource ID cannot be null or whitespace.';

                throw new \Stripe\Exception\InvalidArgumentException($msg);
            }
        }

        return \sprintf($basePath, ...\array_map('\urlencode', $ids));
    }
}
