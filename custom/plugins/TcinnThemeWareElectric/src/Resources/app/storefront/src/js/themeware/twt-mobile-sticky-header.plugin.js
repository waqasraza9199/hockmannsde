/*
    _____ _                __      __
   |_   _| |_  ___ _ __  __\ \    / /_ _ _ _ ___  (R)
     | | | ' \/ -_) '  \/ -_) \/\/ / _` | '_/ -_)
     |_| |_||_\___|_|_|_\___|\_/\_/\__,_|_| \___|

    Copyright (c) TC-Innovations GmbH
    ==================================================
    Plugin "twtMobileStickyHeader"
    see Tab "Header" > Block "Layout" > Section "Basic configuration" > Field "Layout type" > Option "Full width sticky header.."

*/

import Plugin from 'src/plugin-system/plugin.class';

/* TODO
 * - use plain js...
 */

/*************************************************************************
 ** ThemeWare: Mobile sticky header
 *************************************************************************/

/* Info:
 *
 * DE: Das Script soll sich um die mobile Variante des Sticky headers kümmern. Derzeit ist noch alles in einem
 * Script gebündelt.
 */

export default class twtMobileStickyHeader extends Plugin {
    init() {

        // Check if "Full width sticky header / Header 10" (twt-header-type 10) is active #customHeader
        if ($('body.themeware').hasClass('twt-header-type-10')) {
            var twtFullWidthStickyHeader = true;
        } else {
            var twtFullWidthStickyHeader = false;
        }

        // function resize() {
        //
        // }

        // if (!twtFullWidthStickyHeader) {
        //     window.addEventListener('load', resize);
        //     window.addEventListener('scroll', resize);
        // }

    }

}