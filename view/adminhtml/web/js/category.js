define([
    'jquery',
    'Magento_Ui/js/form/components/button',
    'Magento_Ui/js/modal/modal',
    'uiRegistry',
    'mage/url'
], function ($, Button, modal, registry, urlBuilder) {
    'use strict';

    return Button.extend({
        defaults: {
            title: 'AI Content',
            error: '',
            displayArea: '',
            template: 'Riverstone_AiContentGenerator/btn',
            elementTmpl: 'Riverstone_AiContentGenerator/btn',
            actions: [{
                targetName: '${ $.name }',
                actionName: 'action'
            }],
            modalName: 'custom-modal'
        },
        /**
         * @abstract
         */
        onRender: function () {
        },

        hasAddons: function (){
            return false;
        },
        hasService: function () {
            return false;
        },
        action: function(){

            var selector = '[name="' + this.settings.type + '"]';
            var inputElement = $(selector);
            var attributeValue = inputElement.val();

            let product_name = $("input[name='name']").val();
            if (product_name == null || product_name == '' || product_name == undefined) {
                product_name = $("h1[class='page-title']").val();
            }

            var default_prompt = this.settings.prompt;
            console.log(default_prompt)
            var replacedString = default_prompt.replace('%s', product_name);
            console.log(attributeValue)
            $('#current_attribute_value').val(attributeValue);
            $('textarea#default_prompt_value').val(replacedString)
            $('#dynamic-span').text(this.settings.type);
            $('#current_attribute_key').val(this.settings.type);
            $('#generated_content_div').hide();
            $('#generated_content').val("");
            $('#fetch-ai-replace').hide();

            var options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                buttons: []
            };

            var popup = modal(options, $('#custom-page-modal'));
            $('#custom-page-modal').modal('openModal');
        }

    });
});
