<?php

/**
 * @author Cliff Odijk <cliff@jcid.nl>
 * Released under the MIT license.
 */

namespace OAuth\Common\Http\Client;

use OAuth\Common\Http\Client\AbstractClient;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\UriInterface;
use Guzzle\Http\ClientInterface as GuzzleClientInterface;
use Guzzle\Service\Client;

/**
 * Client interface for GuzzlePHP.org
 */
class GuzzleClient extends AbstractClient
{
    /**
     * @var GuzzleClientInterface
     */
    private $client;

    /**
     * Set the Guzzle client
     *
     * @param GuzzleClientInterface $client
     */
    public function setClient(GuzzleClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Get the Guzzle client
     *
     * @return GuzzleClientInterface
     */
    public function getClient()
    {
        if (!$this->client) {
            $this->client = new Client();
            $this->client->setUserAgent($this->userAgent);
        }

        return $this->client;
    }

    /**
     * Any implementing HTTP providers should send a POST request to the provided endpoint with the parameters.
     * They should return, in string form, the response body and throw an exception on error.
     *
     * @param  UriInterface              $endpoint
     * @param  array                     $params
     * @param  array                     $extraHeaders
     * @param  string                    $method
     * @throws TokenResponseException
     * @throws \InvalidArgumentException
     * @return string
     */
    public function retrieveResponse(
        UriInterface $endpoint,
        $requestBody,
        array $extraHeaders = array(),
        $method = 'POST'
    ) {
        // Normalize method name
        $method = strtoupper($method);

        if ($method === 'GET' && !empty($requestBody)) {
            throw new \InvalidArgumentException('No body expected for "GET" request.');
        }

        try {
            $response = $this->getClient()->createRequest($method, $endpoint->getAbsoluteUri(), $extraHeaders, $requestBody)->send();

            if ( $response->getStatusCode() >= 400 ) {
                throw new TokenResponseException('Server returned HTTP response code ' . $response->getStatusCode() );
            }
        } catch (\Guzzle\Http\Exception\CurlException $e) {
            throw new TokenResponseException( 'Guzzle client error: ' . $e->getMessage() );
        }

        return $response->getBody(true);
    }
}