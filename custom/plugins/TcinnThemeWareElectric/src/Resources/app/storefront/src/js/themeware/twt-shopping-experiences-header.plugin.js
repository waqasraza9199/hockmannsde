/*
    _____ _                __      __
   |_   _| |_  ___ _ __  __\ \    / /_ _ _ _ ___  (R)
     | | | ' \/ -_) '  \/ -_) \/\/ / _` | '_/ -_)
     |_| |_||_\___|_|_|_\___|\_/\_/\__,_|_| \___|

    Copyright (c) TC-Innovations GmbH
    ==================================================
    Plugin "twtShoppingExperiencesHeader"
    see Tab "Header" > Block "Shopping Experiences Header"
*/

import Plugin from 'src/plugin-system/plugin.class';

/* TODO
 * - use plain js...
 */

/*************************************************************************
 ** ThemeWare: Calc header for "Shopping Experiences Header"
 *************************************************************************/


/* Info:
 * Script can be toggled via the theme configuration.
 *
 * Set '.header-main > top' | as it is positioned absolute
 * Set '.nav-main > top' | as it is positioned absolute
 * Set '.search-container > top' | as it is positioned absolute
 *
 * DE: Das Script regelt den "Erlebniswelt-Header" (nur wenn der "Full width sticky header" nicht aktiv ist).
 */

export default class twtShoppingExperiencesHeader extends Plugin {
    init() {
        // Init variables
        let twtFullWidthStickyHeader;

        // Check if "Full width sticky header / Header 10" (twt-header-type 10) is active #customHeader
        if ($('body.themeware').hasClass('twt-header-type-10')) { // TODO
            twtFullWidthStickyHeader = true;
        } else {
            twtFullWidthStickyHeader = false;
        }

        // Only run if the "Full width sticky header / Header 10" (twt-header-type 10) is not configured
        if (!twtFullWidthStickyHeader) {
            // Add event listeners
            window.addEventListener('load', setHeader);
            window.addEventListener('scroll', windowScroll);
            //window.addEventListener("resize", windowResize);

            // Initial use of main functions
            setHeader();
        }

        function setHeader() {
            // Init variables
            let twtAnnouncementBannerHeight;
            let twtHeaderHeight;
            let twtNavMainHeight;
            let twtUspBarHeight;

            let twtHeaderMainPosition;
            let twtNavMainPosition;
            let twtSearchContainerPosition;

            // Set variables
            twtAnnouncementBannerHeight = parseInt($(".twt-announcement-banner.is-header").outerHeight(true), 10); // TODO
            twtHeaderHeight = parseInt($('.header-main').outerHeight(true), 10); // TODO
            twtNavMainHeight = parseInt($(".nav-main").outerHeight(true), 10); // TODO
            twtUspBarHeight = parseInt($(".twt-usp-bar.is-header").outerHeight(true), 10); // TODO

            twtHeaderMainPosition = 0;
            twtNavMainPosition = twtHeaderHeight;
            twtSearchContainerPosition = twtHeaderHeight + twtNavMainHeight;

            // Check if 'Announcement banner' is used
            if($('.twt-announcement-banner.is-header').length) { // TODO
                twtHeaderMainPosition = twtHeaderMainPosition + twtAnnouncementBannerHeight;
                twtNavMainPosition = twtNavMainPosition + twtAnnouncementBannerHeight;
                twtSearchContainerPosition = twtSearchContainerPosition + twtAnnouncementBannerHeight;
            }

            // Check if 'USP bar' is used
            if($('.twt-usp-bar.is-header').length) { // TODO
                twtHeaderMainPosition = twtHeaderMainPosition + twtUspBarHeight;
                twtNavMainPosition = twtNavMainPosition + twtUspBarHeight;
                twtSearchContainerPosition = twtSearchContainerPosition + twtUspBarHeight;
            }

            // Apply values only above 568px window width
            if(window.innerWidth > 568) {
                document.querySelector('.header-main').style.top = twtHeaderMainPosition + 'px';
                document.querySelector('.nav-main').style.top = twtNavMainPosition + 'px';
                document.querySelector('.search-container').style.top = twtSearchContainerPosition + 'px';
            } else {
                document.querySelector('.header-main').style.top = 'inherit';
                document.querySelector('.nav-main').style.top = 'inherit';
                document.querySelector('.search-container').style.top = 'inherit';
            }

            /*
            // Check if USP bar is used in the header
            if ($('.twt-usp-bar').hasClass('is-header')) {
                // Set nav-main position (top) > UPS bar height + header height
                $('.nav-main').css('top', twtHeaderHeight + twtUspBarHeight + 'px');
            } else {
                // Set nav-main position (top) > header height
                $('.nav-main').css('top', twtHeaderHeight + 'px');
            }

            // Check if header type 4 is active
            if ($('body.is-act-home').hasClass('twt-header-type-4')) {
                // Set search container position (top) >  Usp bar height + header height + main navigation height)
                $('.search-container').css('top', twtHeaderHeight + twtUspBarHeight + twtNavMainHeight + 'px');
            }
            */
        }

        /* --- Scroll function --- */

        function windowScroll() {
            setHeader();
        }


        /* --- Resize function --- */

        /*function windowResize() {
            setHeader();
        }*/

    }
}