// import all necessary storefront plugins
import NvpVariantSwitch from './js/variant-switch/nvp-variant-switch.plugin';

// register them via the existing PluginManager
const PluginManager = window.PluginManager;
PluginManager.override('VariantSwitch', NvpVariantSwitch, '[data-variant-switch]');
