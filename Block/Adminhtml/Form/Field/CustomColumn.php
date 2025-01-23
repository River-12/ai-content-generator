<?php
namespace Riverstone\AiContentGenerator\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Context;
use Riverstone\AiContentGenerator\Model\ProductAttributeList;

/**
 * Admin Configuration Dynamic form field
 */
class CustomColumn extends Select
{

    /**
     * @var \Riverstone\AiContentGenerator\Model\ProductAttributeList
     */
    protected $productAttributeList;

    /**
     * Admin Configuration Dynamic form field

     * @param Context $context
     * @param \Riverstone\AiContentGenerator\Model\ProductAttributeList $productAttributeList
     * @param array $data
     */

    public function __construct(
        Context $context,
        ProductAttributeList $productAttributeList,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productAttributeList = $productAttributeList;
    }

    /**
     * @inheritDoc
     */
    public function setInputName($value)
    {
        return $this->setName($value.'[]');
    }

    /**
     * @inheritDoc
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML

     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->productAttributeList->toOptionArray());
        }

        return parent::_toHtml();
    }

    /**
     * Add validation class and attributes.
     *
     * @return string
     */
    public function getHtmlAttributes()
    {
        $attributes = parent::getHtmlAttributes();
        $attributes .= ' class="validate-no-duplicates required-entry"';
        // Add other attributes as needed
        return $attributes;
    }
    
    /**
     * Render Yes/No dropdown options

     * @return array
     */
    private function getSourceOptions()
    {
        return [
            ['label' => 'Yes','value' => '1'],
            ['label' => 'No','value' => '0'],
        ];
    }
}
