<?php
namespace Riverstone\AiContentGenerator\Model\Config\Source;

class SearchEngineFields implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Predefined Meta fields

     * @return array
     */
    public function toOptionArray()
    {
        return [
        ['value' => 'meta_title', 'label' => __('Meta Title')],
        ['value' => 'meta_description', 'label' => __('Meta Description')],
        ['value' => 'meta_keywords', 'label' => __('Meta Keywords')]
        ];
    }
}
