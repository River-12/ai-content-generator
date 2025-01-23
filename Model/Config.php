<?php
namespace Riverstone\AiContentGenerator\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    public const XML_PATH_ENABLED = 'riverstone_aicontentgenerator/general/enabled';
    public const XML_PATH_BASE_URL = 'riverstone_aicontentgenerator/api/base_url';
    public const XML_PATH_TOKEN = 'riverstone_aicontentgenerator/api/token';
    public const XML_PATH_MODEL = 'riverstone_aicontentgenerator/api/model';
    public const XML_PATH_STORES = 'riverstone_aicontentgenerator/general/stores';
    public const XML_PATH_PROMPT_META_TITLE = 'riverstone_aicontentgenerator/prompt/meta_title';
    public const XML_PATH_PROMPT_META_DESCRIPTION = 'riverstone_aicontentgenerator/prompt/meta_description';
    public const XML_PATH_PROMPT_META_KEYWORDS = 'riverstone_aicontentgenerator/prompt/meta_keywords';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * General Configurations

     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get admin config value

     * @param string $path
     * @param ScopeConfigInterface $scopeType
     * @param string $scopeCode
     * @return mixed
     */
    public function getValue(string $path, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    /**
     * Get the store ids

     * @return int[]
     */
    public function getEnabledStoreIds(): array
    {
        $stores = $this->scopeConfig->getValue(self::XML_PATH_STORES);

        if ($stores === null || $stores === '') {
            $storeList = [0];
        } else {
            $storeList = $stores === '0' ? [0] : array_map('intval', explode(',', $stores));
        }
        sort($storeList, SORT_NUMERIC);

        return $storeList;
    }
}
