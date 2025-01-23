<?php
namespace Riverstone\AiContentGenerator\Model\OpenAI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class ApiClient
{
    private const DEFAULT_REQUEST_TIMEOUT = 60;

    /**
     * @var Client
     */
    protected $client;

    /**
     * API construct file

     * @param string $baseUrl
     * @param string $token
     */
    public function __construct(string $baseUrl, string $token)
    {
        $config = [
            'base_uri' => $baseUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ];

        $this->client = new Client($config);
    }

    /**
     * Post request API to get content

     * @param string $url
     * @param array $data
     * @param array $options
     * @return ResponseInterface
     */
    public function post(string $url, array $data, ?array $options = []): ResponseInterface
    {
        try {
            return $this->client->post($url, $this->getPreparedOptions($options, $data));
        } catch (BadResponseException $e) {
            return $e->getResponse();
        }
    }

    /**
     * Get prepared options

     * @param string $options
     * @param array $data
     * @return array
     */
    protected function getPreparedOptions($options, $data): array
    {
        $options[RequestOptions::JSON] = $data;

        if (!isset($options['timeout'])) {
            $options['timeout'] = self::DEFAULT_REQUEST_TIMEOUT;
        }

        if (!isset($options['connect_timeout'])) {
            $options['connect_timeout'] = self::DEFAULT_REQUEST_TIMEOUT;
        }

        return $options;
    }
}
