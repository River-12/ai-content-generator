<?php
namespace Riverstone\AiContentGenerator\Block\Adminhtml\Category;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Riverstone\AiContentGenerator\Block\Adminhtml\Form\Field\CategoryColumn;

class DynamicCategoryFields extends AbstractFieldArray
{
    /**
     * @var CategoryColumn
     */
    private $dropdownRenderer;

    /**
     * Prepare existing row data object

     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'category_field',
            [
                'label' => __('Category Fields'),
                'class' => 'validate-no-duplicates',
                'renderer' => $this->getDropdownRenderer(),
            ]
        );

        $this->addColumn(
            'prompt',
            [
                'label' => __('Prompt'),
                'class' => 'required-entry',
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare dropdown options

     * @param DataObject $row
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        
        $options = [];
        $dropdownField = $row->getDropdownField();

        if ($dropdownField !== null) {
            $dropDownOptions = $this->getDropdownRenderer()->calcOptionHash($dropdownField);
            $options['option_' . $dropDownOptions] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * Dropdown renderer

     * @return CategoryColumn
     * @throws LocalizedException
     */
    private function getDropdownRenderer()
    {
        if (!$this->dropdownRenderer) {
            $render = ['is_render_to_js_template' => true];
            $this->dropdownRenderer = $this->getLayout()->createBlock(CategoryColumn::class, '', ['data' => $render]);
        }
        return $this->dropdownRenderer;
    }
}
