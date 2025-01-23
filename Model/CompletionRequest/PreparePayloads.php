<?php


namespace Riverstone\AiContentGenerator\Model\CompletionRequest;

use Riverstone\AiContentGenerator\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;

class PreparePayloads
{

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Prepare payload file

     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Prepare API payloads

     * @param string $text
     * @return array
     */
    public function getApiPayload(string $text): array
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/customagal12.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('getAPipayload');
        $this->validateRequest($text);
        $model = $this->scopeConfig->getValue(Config::XML_PATH_MODEL);
        $logger->info($model);
        $payload =  [
            "model" => $model,
            "n" => 1,
            "temperature" => 0.5,
            "max_tokens" => 500,
            "frequency_penalty" => 0,
            "presence_penalty" => 0
        ];
        $logger->info('Intermediate Payload: ' . json_encode($payload, JSON_PRETTY_PRINT));
        if (strpos($model, 'gpt') !== false) {
            $payload['messages'] = [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful assistant.',
                ],
                [
                    'role' => 'user',
                    'content' => $text,
                ],
            ];
        } else {
            $payload['prompt'] = $text;
        }
        $logger->info('Final Payload: ' . json_encode($payload, JSON_PRETTY_PRINT));
        return $payload;
    }

    /**
     * Validating the prompt string lenght

     * @param string $prompt
     * @return string
     */
    protected function validateRequest(string $prompt): void
    {
        if (empty($prompt) || strlen($prompt) < 10) {
            throw new InvalidArgumentException('Invalid query (must be at least 10 characters)');
        }
    }
}
