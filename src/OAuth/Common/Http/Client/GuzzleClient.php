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
use Guzzle\Http\Exception\CurlException;
use Guzzle\Http\Exception\BadResponseException;

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

            // Create request
            $request = $this->getClient()->createRequest(
                $method,
                $endpoint->getAbsoluteUri(),
                $extraHeaders,
                $requestBody
            );
            $request->getParams()->set('redirect.max', $this->maxRedirects);
            $request->getCurlOptions()->set(CURLOPT_TIMEOUT, $this->timeout);

            // Get response
            $response = $request->send();

            // Check response
            if ($response->getStatusCode() >= 400) {
                throw new TokenResponseException('Server returned HTTP response code '.$response->getStatusCode());
            }
        } catch (BadResponseException $e) {
            throw new TokenResponseException('Guzzle client error: ' . $e->getMessage(), null, $e);
        } catch (CurlException $e) {
            throw new TokenResponseException('Guzzle client error: ' . $e->getMessage(), null, $e);
        }

        return $response->getBody(true);
    }
}
