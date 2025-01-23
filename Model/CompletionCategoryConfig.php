<?php
namespace Riverstone\AiContentGenerator\Model;

use Riverstone\AiContentGenerator\Api\CompletionRequestInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Riverstone\AiContentGenerator\Model\Config\Source\SearchEngineFields;

class CompletionCategoryConfig
{
    private const XML_PATH_CATEGORY_FIELDS = 'riverstone_aicontentgenerator/category_settings/category_fields';
    private const XML_PATH_CATEGORY_PAGE_ENABLED = 'riverstone_aicontentgenerator/category_settings/enabled';
    private const META_CATEGORY_PAGE = 'category_form.category_form.search_engine_optimization';

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serialize;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var SearchEngineFields
     */
    private $searchEngineFields;

    /**
     * Categrory Ui component file

     * @param Config $config
     * @param RequestInterface $request
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Serialize\Serializer\Json $serialize
     * @param SearchEngineFields $searchEngineFields
     */

    public function __construct(
        Config $config,
        RequestInterface $request,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Serialize\Serializer\Json $serialize,
        SearchEngineFields $searchEngineFields
    ) {
        $this->config = $config;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->serialize = $serialize;
        $this->searchEngineFields = $searchEngineFields;
    }

    /**
     * Get all added attribute to add the button using UI component

     * @return array
     */
    public function getConfig(): array
    {
        $targets = [];
        $productPagePath = self::META_CATEGORY_PAGE;

        $getDefaultAttribute = $this->searchEngineFields->toOptionArray();

        if ((!$this->config->getValue(Config::XML_PATH_ENABLED)) || (!$this->scopeConfig->getValue(self::XML_PATH_CATEGORY_PAGE_ENABLED, ScopeInterface::SCOPE_STORE))) {
            foreach ($getDefaultAttribute as $option) {
                $value = $option['value'];
                $targets[$value] = [
                    'container' => $productPagePath.".".$value."_group",
                    'visible' => false
                ];
            }
            $targets = array_filter($targets);
    
            return [
                'targets' => $targets
            ];
        }

        $selectedAttributes = $this->getCategoryFields();

        $notPresent=[];
        foreach ($getDefaultAttribute as $item1) {
            $field_name = $item1['value'];
            $found = false;
        
            foreach ($selectedAttributes as $item2) {
                $value = $item2['field_name'];
        
                if ($field_name === $value) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $notPresent[] = $field_name;
            }
        }
        foreach ($notPresent as $notpresent) {
            $targets[$notpresent] = [
                'container' => $productPagePath.".".$notpresent."_group",
                'visible' => 0
            ];
        }

        foreach ($selectedAttributes as $attribute) {
            $targets[$attribute['field_name']] = [
                'attribute_label' => $attribute['field_name'],
                'container' => $productPagePath.".".$attribute['field_name']."_group",
                'prompt_from' => $productPagePath.".".$attribute['field_name'],
                'target_field' =>  $productPagePath.".".$attribute['field_name']."_group",
                'component' => 'Riverstone_AiContentGenerator/js/category',
                'prompt' => $attribute['prompt'],
                'visible' => 1,
            ];
        }

        $targets = array_filter($targets);

        return [
            'targets' => $targets
        ];
    }

    /**
     * Get the type of field

     * @param string $type
     * @return null
     */
    public function getByType(string $type)
    {
        return null;
    }

    /**
     * Getting the admin configuration value and unserializing

     * @return array
     */
    public function getCategoryFields()
    {
      
        $config = $this->scopeConfig->getValue(self::XML_PATH_CATEGORY_FIELDS, ScopeInterface::SCOPE_STORE);

        if ($config == '' || $config == null):
            return;
        endif;

        $unserializedata = $this->serialize->unserialize($config);
        $attributeArray = [];
        foreach ($unserializedata as $key => $row) {
            $attributeArray[] = ['field_name' => $row['category_field'][0] , 'prompt' => $row['prompt']];
        }
        return $attributeArray;
    }
}
