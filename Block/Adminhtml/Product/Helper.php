<?php
namespace Riverstone\AiContentGenerator\Block\Adminhtml\Product;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Api\StoreRepositoryInterface;
use Riverstone\AiContentGenerator\Model\Config;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * AI Content generator helper file
 */
class Helper extends Template
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var Json
     */
    private $json;

    /**
     * Helper file

     * @param Context $context
     * @param Config $config
     * @param StoreRepositoryInterface $storeRepository
     * @param LocatorInterface $locator
     * @param Json $json
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $config,
        StoreRepositoryInterface $storeRepository,
        LocatorInterface $locator,
        Json $json,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->storeRepository = $storeRepository;
        $this->locator = $locator;
        $this->json = $json;
    }

    /**
     * Default component json configuration

     * @return string
     */
    public function getComponentJsonConfig(): string
    {
        $config = [
            'serviceUrl' => $this->getUrl('Riverstone_AiContentGenerator/helper/validate'),
            'sku' => $this->locator->getProduct()->getSku(),
            'storeId' => $this->locator->getStore()->getId(),
            'stores' => $this->getStores()
        ];
        return $this->json->serialize($config);
    }

    /**
     * Get store details

     * @return array
     */
    public function getStores(): array
    {
        $selectedStoreId = (int) $this->locator->getStore()->getId();
        $storeIds = $this->config->getEnabledStoreIds();

        $results = [];
        $first = null;
        foreach ($storeIds as $storeId) {
            $store = $this->storeRepository->getById($storeId);
            if ($selectedStoreId === $storeId) {
                $first = $store;
                continue;
            }
            $results[] = [
                'label' => $storeId === 0 ? __('Default scope') : $store->getName(),
                'store_id' => $storeId,
                'selected' => false
            ];
        }

        if ($first) {
            array_unshift($results, [
                'label' => __('Current scope'),
                'store_id' => $first->getId(),
                'selected' => true
            ]);
        }

        return $results;
    }

    /**
     * Convert to html if the Extension is enabled

     * @return string
     */
    public function toHtml(): string
    {
        $enabled = $this->config->getValue(Config::XML_PATH_ENABLED);
        if (!$enabled) {
            return '';
        }
        return parent::toHtml();
    }
}
