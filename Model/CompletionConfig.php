<?php

namespace Riverstone\AiContentGenerator\Model;

use Riverstone\AiContentGenerator\Api\CompletionRequestInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Eav\Model\Entity\Attribute;

class CompletionConfig
{
    private const XML_PATH_PRODUCT_ATTRIBUTES = 'riverstone_aicontentgenerator/product_settings/attributes';
    private const XML_PATH_PRODUCT_PAGE_ENABLED = 'riverstone_aicontentgenerator/product_settings/enabled';  
    private const META_PRODUCT_PAGE = 'product_form.product_form.search-engine-optimization';
    private const DESCP_PRODUCT_PAGE = 'product_form.product_form.content';
    private const CUSTOM_ATTRIBUTE_PRODUCT_PAGE = 'product_form.product_form.product-details';
    private const ENTITY_TYPE = 'catalog_product';

    /**
     * @var Attribute
     */
    protected $attributeDetails;

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
     * Categrory Ui component file

     * @param Config $config
     * @param RequestInterface $request
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Serialize\Serializer\Json $serialize
     */
    public function __construct(
        Config $config,
        RequestInterface $request,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Serialize\Serializer\Json $serialize,
        Attribute $attributeDetails
    ) {
        $this->config = $config;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->serialize = $serialize;
        $this->attributeDetails = $attributeDetails;
    }

    /**
     * Get all added attribute to add the button using UI component

     * @return array
     */
    public function getConfig(): array
    {       
        if ((!$this->config->getValue(Config::XML_PATH_ENABLED)) || (!$this->scopeConfig->getValue(self::XML_PATH_PRODUCT_PAGE_ENABLED, ScopeInterface::SCOPE_STORE))) {
            return [
                'targets' => []
            ];
        }

        $selectedAttributes = $this->getProductAttributes();

        $targets = [];
        foreach ($selectedAttributes as $attribute) {

            if (stripos($attribute['attribute_code'], 'meta') !== false) {
                $productPagePath = self::META_PRODUCT_PAGE;
            } elseif (stripos($attribute['attribute_code'], 'description') !== false) {
                $productPagePath = self::DESCP_PRODUCT_PAGE;
            } else{
                $productPagePath = self::CUSTOM_ATTRIBUTE_PRODUCT_PAGE;
            }

            $targets[$attribute['attribute_code']] = [
                'attribute_label' => $attribute['attribute_code'],
                'container' => $productPagePath.".container_".$attribute['attribute_code'],
                'prompt_from' => $productPagePath.".".$attribute['attribute_code'],
                'target_field' =>  $productPagePath.".".$attribute['attribute_code'],
                'component' => 'Riverstone_AiContentGenerator/js/button',
                'prompt' => $attribute['prompt'],
                'attributeType' => $attribute['attributeType'],
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
    public function getProductAttributes()
    {
      
        $config = $this->scopeConfig->getValue(self::XML_PATH_PRODUCT_ATTRIBUTES, ScopeInterface::SCOPE_STORE);

        if ($config == '' || $config == null):
            return;
        endif;

        $unserializedata = $this->serialize->unserialize($config);
        $attributeArray = [];
        foreach ($unserializedata as $key => $row) {
            $attributeType = $this->attributeDetails->loadByCode(self::ENTITY_TYPE, $row['product_attribute'][0]);
            $attributeArray[] = ['attribute_code' => $row['product_attribute'][0] , 'prompt' => $row['prompt'], 'attributeType' => $attributeType->getIsWysiwygEnabled()];
        }
        return $attributeArray;
    }
}
