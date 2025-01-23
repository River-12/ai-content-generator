<?php
namespace Riverstone\AiContentGenerator\Model;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Data\OptionSourceInterface;

class ProductAttributeList implements OptionSourceInterface
{

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $attributeFactory;

    /**
     * Get list of product text and textarea attribute for dynamic row dropdown

     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeFactory
    ) {
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * Get list of product text and textarea attribute in array

     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->attributeFactory->create();
        $collection->addFieldToFilter('frontend_input', ['in' => ['text', 'textarea']]);
        $collection->addFieldToFilter('frontend_label', ['notnull' => true]);
        $collection->addFieldToFilter('attribute_model', ['null' => true]);
        $collection->addFieldToFilter('backend_model', ['null' => true]);
        $collection->addFieldToFilter('frontend_model', ['null' => true]);
        $collection->addFieldToFilter('source_model', ['null' => true]);
        $collection->addFieldToFilter('is_pagebuilder_enabled', ['eq' => 0]);
        $collection->addFieldToFilter('is_visible', ['eq' => 1]);
        $collection->addFieldToFilter('backend_type', ['in' => ['text', 'varchar']]);
        $collection->addFieldToFilter('attribute_code', ['nlike' => '%url%']);
        $collection->setOrder('frontend_label', 'ASC');

        $options = [];
        foreach ($collection as $attribute) {
            $options[] = ['value' => $attribute->getAttributeCode(), 'label' => $attribute->getFrontendLabel()];
        }
        return $options;
    }
}
