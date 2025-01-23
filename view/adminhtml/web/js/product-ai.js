define([
    'uiComponent',
    'mageUtils',
    'uiRegistry',
    'uiLayout',
    'Magento_Ui/js/lib/spinner',
    'underscore'
], function (Component, utils, Registry, layout, loader, _) {
    'use strict';
    return Component.extend({
        defaults: {
            targets: {},
            settings: {},
        },
        initialize: function () {
            this._super();
            for (const [key, group] of Object.entries(this.targets)) {
                this.containerReady(group.container)
                    .then((component) => {
                        this.createComponents(key, group, component);
                    });
            }
        },
        hasAddons: function (){
            return false;
        },
        hasService: function () {
            return false;
        },
        containerReady: function (component) {
            return new Promise((resolve) => {
                Registry.get(component, (component) => {
                    component.elems.subscribe(() => {
                        resolve(component);
                    });
                });
            });
        },

        createComponents: function (type, groupConfig, parent) {
            var prompt = groupConfig.prompt;
            var attributeType = groupConfig.attributeType;
            const settings = {
                ...this.settings,
                ...groupConfig,
                type,
                prompt,
                attributeType
            };

            const buttonConfig = {
                parent: parent.name,
                name: 'riverstone-ai-button-' + type,
                component: groupConfig.component,
                config: {
                    settings,
                    modalName: this.name + '.' + type + '-modal',
                    loader: loader.get('product_form.product_form')
                }
            };

            layout([buttonConfig]);
        }
    });
});
