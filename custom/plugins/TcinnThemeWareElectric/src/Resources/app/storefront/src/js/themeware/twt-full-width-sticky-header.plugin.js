/*
    _____ _                __      __
   |_   _| |_  ___ _ __  __\ \    / /_ _ _ _ ___  (R)
     | | | ' \/ -_) '  \/ -_) \/\/ / _` | '_/ -_)
     |_| |_||_\___|_|_|_\___|\_/\_/\__,_|_| \___|

    Copyright (c) TC-Innovations GmbH
    ==================================================
    Plugin "twtFullWidthStickyHeader"
    see Tab "Header" > Block "Layout" > Section "Basic configuration" > Field "Layout type" > Option "Full width sticky header.."

*/

import Plugin from 'src/plugin-system/plugin.class';

/* TODO
 * - improve onload as element is not positioned sticky when window is too small...
 */

/*************************************************************************
 ** ThemeWare: Full-width sticky header #customHeader
 *************************************************************************/

/* Info:
 *
 * DE: Das Script berechnet den "Full width sticky header". Bei diesem müssen diverse Header-Elmenete positioniert und
 * berechnet werden.
 */

export default class twtFullWidthStickyHeader extends Plugin {
    init() {
        // Init variables
        let twtHeaderClass = "twt-is-sticky-header";
        
        let twtHeaderExperiencesSearchTop;
        
        let headerHeight;
        let headerScrollDistance;

        let navigationFlyoutTop;
        let navigationFlyoutTopSticky;

        let twtNavMainType = $('#twt-data-attributes').attr('data-twt-top-navigation-type'); // TODO
        let twtSearchType = $('#twt-data-attributes').attr('data-twt-search-type'); // TODO
        let twtShoppingExperiencesHeader = $('#twt-data-attributes').attr('data-twt-shopping-experiences-header'); // TODO

        let minWindowWidth = 576;

        let isMobile;
        let isSticky;

        // Add event listeners
        window.addEventListener("onload", setElements);
        window.addEventListener("scroll", windowScroll);
        window.addEventListener("resize", windowResize);

        // Initial use of main functions
        setElements();


        /* --- Set variables function --- */

        function setVariables() {
            // Init variables
            let twtAnnouncementBannerHeight;
            let twtHeaderPaddingBottom;
            let twtUspBarHeight;

            // Get header height
            headerHeight = $("header.header-main").outerHeight(true); // TODO

            // Check if "Usp bar" is active
            if ($('.twt-usp-bar.is-header').length) { // TODO
                twtUspBarHeight = $(".twt-usp-bar.is-header").outerHeight(true); // TODO
            } else {
                twtUspBarHeight = 0;
            }

            // Check if "Announcement banner" is active
            if ($('.twt-announcement-banner.is-header').length) { // TODO
                twtAnnouncementBannerHeight = $(".twt-announcement-banner.is-header").outerHeight(true); // TODO
            } else {
                twtAnnouncementBannerHeight = 0;
            }

            // Calculate scroll distance for the header
            headerScrollDistance = twtUspBarHeight + twtAnnouncementBannerHeight;

            // Calculate flyout navigation position (top)
            twtHeaderPaddingBottom = parseInt($("header.header-main .header-row").css('padding-bottom'), 10); // TODO
            
            // Calculate search position (top) for the "Shopping Experiences Header"
            twtHeaderExperiencesSearchTop = twtUspBarHeight + twtAnnouncementBannerHeight + headerHeight;

            // Calculate navigation flyout position (top) when not sticky
            navigationFlyoutTop = headerHeight + twtUspBarHeight + twtAnnouncementBannerHeight - twtHeaderPaddingBottom;
            
            // Calculate navigation flyout position (top) when sticky
            navigationFlyoutTopSticky = headerHeight - twtHeaderPaddingBottom;
        }


        /* --- Scroll function --- */

        function windowScroll() {
            setElements();
        }


        /* --- Resize function --- */

        function windowResize() {
            setElements();
        }


        /* --- Set  header elements function --- */

        function setElements() {
            setVariables();

            isSticky = $(window).scrollTop() > headerScrollDistance; // TODO
            /*if($(window).scrollTop() > headerScrollDistance) {
                // is sticky
                isSticky = true;
            } else {
                // is NOT sticky
                isSticky = false;
            }*/

            isMobile = $(window).width() < minWindowWidth; // TODO
            /*if ($(window).width() < minWindowWidth) {
                // @mobile (< 576px)
                isMobile = true;
            } else {
                // @tablet/desktop (>= 576px)
                isMobile = false;
            }*/

            // Set negative margin if "Shopping Experiences Header" is used to pull the content behind the header
            if (twtShoppingExperiencesHeader) {
                if (isMobile) {
                    document.querySelector('.content-main').style.marginTop = 'inherit';
                } else {
                    document.querySelector('.content-main').style.marginTop = -headerHeight + 'px';
                }
            }

            if (isSticky) {
                // is sticky

                document.querySelector('body').classList.add(twtHeaderClass);

                if (isMobile) {
                    document.querySelector('body').style.paddingTop = 'inherit';
                } else {
                    document.querySelector('body').style.paddingTop = headerHeight + 'px';
                }

                if(twtNavMainType === '2') { // Option: "Show list"
                    document.querySelector('.navigation-flyouts').style.top = navigationFlyoutTopSticky + 'px';
                }

                if(twtSearchType === '2') {
                    // Don't apply if flyout search isn't used
                    document.getElementById('searchCollapse').style.top = headerHeight + 'px';
                }
                
            } else {
                // NOT sticky

                document.querySelector('body').classList.remove(twtHeaderClass);

                document.querySelector('body').style.paddingTop = 'inherit';

                if(twtNavMainType === '2') { // Option: "Show list"
                    document.querySelector('.navigation-flyouts').style.top = navigationFlyoutTop - $(window).scrollTop() + 'px'; // TODO
                }
                
                if(twtSearchType === '2') {
                    // Don't apply if flyout search isn't used
                    document.getElementById('searchCollapse').style.top = 'inherit';
                }
            }
        }
        
    }
}