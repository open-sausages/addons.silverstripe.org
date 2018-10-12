<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Retrieves Github repository information about a package
 *
 * @package mysite
 */
class GitHubRepoService extends Object
{
    /**
     * The Guzzle client
     *
     * @var GuzzleHttp\Client
     */
    protected $client;

    /**
     * GitHub API configuration
     * @var string
     */
    const API_BASE_URI        = 'https://api.github.com';

    /**
     * @param  string $repo Repo identifier (not composer package name)
     * @return string JSON
     */
    public function get($markdown)
    {
        $body = '';
        try {
            /** @var Psr\Http\Message\RequestInterface $request */
            $request = $this->getClient()
                ->request(
                    'GET',
                    $this->getEndpoint()
                );
            $body = (string) $request->getBody();
        } catch (RequestException $ex) {
            user_error($ex->getMessage());
            return '';
        }

        return $body;
    }

   /**
     * Get an instance of a GuzzleHttp client
     * @return GuzzleHttp\Client
     */
    public function getClient()
    {
        if (is_null($this->client)) {
            $this->client = new Client(['base_uri' => $this->getBaseUri()]);
        }

        return $this->client;
    }

    /**
     * @return string
     */
    public function getBaseUri()
    {
        return self::API_BASE_URI;
    }

    /**
     * @param String $repo
     * @return String
     */
    public function getEndpoint($repo)
    {
        $endpoint = '/repos' . $repo;

        if (defined('SS_GITHUB_CLIENT_ID') && defined('SS_GITHUB_CLIENT_SECRET')) {
            $endpoint .= sprintf('?client_id=%s&client_secret=%s', SS_GITHUB_CLIENT_ID, SS_GITHUB_CLIENT_SECRET);
        }

        return $endpoint;
    }
}
