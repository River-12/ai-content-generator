<?php
namespace Riverstone\AiContentGenerator\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;

/**
 * Admin Configuration Dynamic form field
 */
class CategoryColumn extends Select
{
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
            $this->setOptions($this->getCategoryFields());
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

    /**
     * Get the predefined fields for show

     * @return array
     */
    private function getCategoryFields()
    {
        return [
            ['value' => 'meta_title', 'label' => __('Meta Title')],
            ['value' => 'meta_description', 'label' => __('Meta Description')],
            ['value' => 'meta_keywords', 'label' => __('Meta Keywords')]
        ];
    }
}
