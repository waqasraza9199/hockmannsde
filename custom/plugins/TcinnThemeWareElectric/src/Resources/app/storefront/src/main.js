/*
    _____ _                __      __
   |_   _| |_  ___ _ __  __\ \    / /_ _ _ _ ___  (R)
     | | | ' \/ -_) '  \/ -_) \/\/ / _` | '_/ -_)
     |_| |_||_\___|_|_|_\___|\_/\_/\__,_|_| \___|

    Copyright (c) TC-Innovations GmbH
	 
*/

// Import all necessary storefront plugins
import twtStickyMainNavigation from './js/themeware/twt-sticky-main-navigation.plugin';
import twtFullWidthStickyHeader from './js/themeware/twt-full-width-sticky-header.plugin'; // #customHeader
import twtShoppingExperiencesHeader from './js/themeware/twt-shopping-experiences-header.plugin';
import twtFloatingWidget from './js/themeware/twt-floating-widget.plugin';
import twtScrollAnimation from './js/themeware/twt-scroll-animation.plugin';
import twtSlideoutCommunities from './js/themeware/twt-slideout-communities.plugin';
//import twtMobileStickyHeader from './js/themeware/twt-mobile-sticky-header.plugin';

// Register them via the existing PluginManager
const PluginManager = window.PluginManager;
PluginManager.register('twtStickyMainNavigation', twtStickyMainNavigation, '[data-twt-sticky-type]'); // Attribute nur verfügbar wenn die "Sticky-Navigation" aktiv ist + nur falls der "Custom-Header" nicht aktiv ist. #customHeader
PluginManager.register('twtFullWidthStickyHeader', twtFullWidthStickyHeader, 'body[class*="twt-header-type-10"]'); // Klasse nur vorhanden, wenn der "Custom-Header" aktiv ist. #customHeader
PluginManager.register('twtShoppingExperiencesHeader', twtShoppingExperiencesHeader, '[data-twt-shopping-experiences-header="true"]'); // Attribute nur verfügbar wenn der "Erlebniswelt-Header" aktiv ist.
PluginManager.register('twtFloatingWidget', twtFloatingWidget, '[id="twt-floating-widget"]'); // Attribute nur verfügbar wenn das "Floating-Widget" aktiv ist.
PluginManager.register('twtScrollAnimation', twtScrollAnimation, 'div[class*="twt-cms-animation"]'); // Plugin wird angewendet sobald ein DIV-Element die entsprechende Klasse hat.
PluginManager.register('twtSlideoutCommunities', twtSlideoutCommunities, '[id="twt-slideout-communities"]'); // Attribute nur verfügbar wenn die "Slideout-Communities" aktiv sind.
//PluginManager.register('twtMobileStickyHeader', twtMobileStickyHeader, '[data-twt-mobile-sticky-header="2"]');