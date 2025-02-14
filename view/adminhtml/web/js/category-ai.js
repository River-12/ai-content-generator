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
                        console.log(component)
                        component.visible(group.visible)
                        if(group.visible){
                            this.createComponents(key, group, component);
                        }
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
                    resolve(component);
                });
            });
        },

        createComponents: function (type, groupConfig, parent) {
            var prompt = groupConfig.prompt;
            const settings = {
                ...this.settings,
                ...groupConfig,
                type,
                prompt
            };

            const buttonConfig = {
                parent: parent.name,
                name: 'riverstone-ai-button-' + type,
                component: groupConfig.component,
                config: {
                    settings,
                    modalName: this.name + '.' + type + '-modal',
                    loader: loader.get('category_form.category_form')
                }
            };

            layout([buttonConfig]);
        }
    });
});
