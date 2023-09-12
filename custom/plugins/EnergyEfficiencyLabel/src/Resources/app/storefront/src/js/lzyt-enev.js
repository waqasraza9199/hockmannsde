import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';

export default class LzytEnev extends Plugin {
    init() {
        this._registerEvents();
    }

    _update() {
        this.init();
    }

    _registerEvents() {
        const listingEl = this._getListingEl();

        if (listingEl) {
            this._getListingPlugin(listingEl).$emitter.subscribe('Listing/afterRenderResponse', () => {
                $('[data-toggle="tooltip"]').tooltip();
            })
        }
    }

    _getListingEl() {
        return DomAccess.querySelector(document, '[data-listing]', false);
    }

    _getListingPlugin(listingEl) {
        return window.PluginManager.getPluginInstanceFromElement(listingEl, 'Listing');
    }
}