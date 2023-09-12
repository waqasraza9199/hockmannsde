// src/Resources/app/storefront/src/js/variant-switch/nvp-variant-switch.plugin.js

import VariantSwitchPlugin from "src/plugin/variant-switch/variant-switch.plugin";
import DomAccess from 'src/helper/dom-access.helper';

export default class NvpVariantSwitch extends VariantSwitchPlugin {
    static options = {
        radioFieldSelector: '.product-detail-configurator-option-input',
        radioFieldCheckedSelector: '.product-detail-configurator-option-input:checked',
        selectFieldSelector: '.product-detail-configurator-select-input',
        selectOptionsSelector: '.product-detail-configurator-select-input option',
        configuratorGroupSelector: '.product-detail-configurator-group-title',
    };

    init() {
        super.init()

        this._radioFields = DomAccess.querySelectorAll(this.el, this.options.radioFieldSelector, false);
        this._selectFields = DomAccess.querySelectorAll(this.el, this.options.selectFieldSelector, false);
        this._configuratorGroup = DomAccess.querySelectorAll(this.el, this.options.configuratorGroupSelector, false);
    }

    /**
     * Wait with Change Event until all configurator groups have been selected.
     *
     * @param event
     * @private
     */
    _onChange(event) {
        let configurationGroupLength = this._configuratorGroup.length;
        let radioFieldsChecked = DomAccess.querySelectorAll(this.el, this.options.radioFieldCheckedSelector, false);
        let radioFieldsCheckedLength = radioFieldsChecked.length;
        let selectedOptionsSelector = DomAccess.querySelectorAll(this.el, this.options.selectOptionsSelector, false);

        let countSelectedOptions = 0;
        for (let i=0; i < selectedOptionsSelector.length; i++) {
            if (selectedOptionsSelector[i].selected && !selectedOptionsSelector[i].disabled) countSelectedOptions++;
        }

        if (configurationGroupLength == radioFieldsCheckedLength + countSelectedOptions) {
            super._onChange(event);
        } else if (configurationGroupLength == countSelectedOptions) {
            super._onChange(event);
        } else if (configurationGroupLength == radioFieldsCheckedLength) {
            super._onChange(event);
        }
    }
}
