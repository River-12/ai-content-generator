<?php

namespace Riverstone\AiContentGenerator\Model\CompletionRequest;

use InvalidArgumentException;
use Laminas\Json\Decoder;
use Laminas\Json\Json;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Riverstone\AiContentGenerator\Model\Config;
use Riverstone\AiContentGenerator\Model\OpenAI\ApiClient;
use Riverstone\AiContentGenerator\Model\OpenAI\OpenAiException;
use Riverstone\AiContentGenerator\Model\CompletionRequest\PreparePayloads;
use Riverstone\AiContentGenerator\Model\Normalizer;

/**
 * General model file
 */
class GeneralCompletion
{
    public const TYPE = '';
    protected const CUT_RESULT_PREFIX = '';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var ScopeConApiClientfigInterface
     */
    protected $apiClient = null;

    /**
     * @var PreparePayloads
     */
    protected $preparePayloads;

    /**
     * Abstract model file
     * @param ScopeConfigInterface $scopeConfig
     * @param PreparePayloads $preparePayloads
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        PreparePayloads $preparePayloads
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->preparePayloads = $preparePayloads;
    }

    /**
     * Checking and setting the prompt to empty
     * @param array $params
     * @return string
     */
    public function getQuery(array $params): string
    {
        return $params['prompt'] ?? '';
    }

    /**
     * Api request function
     * @param string $prompt
     * @return string
     * @throws OpenAiException
     */
    public function query(string $prompt): string
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/customagal.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('payload');

        $payload = $this->preparePayloads->getApiPayload($prompt);
        $logger->info('Final in generalPayload: ' . json_encode($payload, JSON_PRETTY_PRINT));

        $model = $this->scopeConfig->getValue(Config::XML_PATH_MODEL);
        $logger->info('Model: ' . $model);

        $endpoint = strpos($model, 'gpt') !== false ? '/v1/chat/completions' : '/v1/completions';
        $logger->info('Endpoint: ' . $endpoint);

        try {
            $result = $this->getClient()->post($endpoint, $payload);

            $logger->info('Response status: ' . $result->getStatusCode());
            $logger->info('Response body: ' . $result->getBody()->getContents());

            $this->validateResponse($result);
            $convertedResponse = $this->convertToResponse($result->getBody()->getContents());
            $logger->info('Converted Response: ' . $convertedResponse);

            return $convertedResponse;
        } catch (\Exception $e) {
            $logger->err('API Request failed: ' . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Getting the API client request
     * @return ApiClient
     */
    private function getClient(): ApiClient
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/customagal.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('payload');
        $token = $this->scopeConfig->getValue(Config::XML_PATH_TOKEN);
        $logger->info('token'. $token);
        if (empty($token)) {
            throw new InvalidArgumentException('API token is missing');
        }
        if ($this->apiClient === null) {
            $this->apiClient = new ApiClient(
                $this->scopeConfig->getValue(Config::XML_PATH_BASE_URL),
                $this->scopeConfig->getValue(Config::XML_PATH_TOKEN)
            );
        }
        return $this->apiClient;
    }

    /**
     * Validating the response code
     * @param ResponseInterface $result
     * @throws OpenAiException
     */
    protected function validateResponse(ResponseInterface $result): void
    {
        if ($result->getStatusCode() === 401) {
            throw new OpenAiException(__('API unauthorized. Token could be invalid.'));
        }

        if ($result->getStatusCode() >= 500) {
            throw new OpenAiException(__('Server error: %1', $result->getReasonPhrase()));
        }

        $data = Decoder::decode($result->getBody(), Json::TYPE_ARRAY);

        if (isset($data['error'])) {
            throw new OpenAiException(__(
                '%1: %2',
                $data['error']['type'] ?? 'unknown',
                $data['error']['message'] ?? 'unknown'
            ));
        }

        if (!isset($data['choices'])) {
            throw new OpenAiException(__('No results were returned by the server'));
        }
    }

    /**
     * Converting the  Stream response to json
     * @param StreamInterface $stream
     * @return string
     * @throws OpenAiException
     */
    public function convertToResponse(StreamInterface $stream): string
    {
        $streamText = (string)$stream;
        $data = Decoder::decode($streamText, Json::TYPE_ARRAY);

        $choices = $data['choices'] ?? [];
        $textData = reset($choices);

        $text = $textData['text'] ?? '';
        $text = trim($text);
        $text = trim($text, '"');

        if (empty($text)) {
            $text = $textData['message']['content'] ?? '';
        }
        if (substr($text, 0, strlen(static::CUT_RESULT_PREFIX)) == static::CUT_RESULT_PREFIX) {
            $text = substr($text, strlen(static::CUT_RESULT_PREFIX));
        }

        return $text;
    }

    /**
     * Get the type of field
     * @return string
     */
    public function getType(): string
    {
        return static::TYPE;
    }
}
