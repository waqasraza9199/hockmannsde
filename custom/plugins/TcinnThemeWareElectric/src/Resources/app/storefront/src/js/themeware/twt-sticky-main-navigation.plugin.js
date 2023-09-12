/*
    _____ _                __      __
   |_   _| |_  ___ _ __  __\ \    / /_ _ _ _ ___  (R)
     | | | ' \/ -_) '  \/ -_) \/\/ / _` | '_/ -_)
     |_| |_||_\___|_|_|_\___|\_/\_/\__,_|_| \___|

    Copyright (c) TC-Innovations GmbH
    ==================================================
    Plugin "twtStickyMainNavigation"
    see Tab "Header" > Block "Sticky top navigation"
*/

import Plugin from 'src/plugin-system/plugin.class';

/* TODO
 * - use plain js...
 * - separate js for mobile-sticky...
 * - add announcement banner to calculation...
 * - add resize function to script...
 */

/*************************************************************************
 ** ThemeWare: Sticky main navigation
 *************************************************************************/

/* Info:
 * Script can be toggled via the theme configuration.
 *
 * DE: Das Script kümmert sich um den Sticky header (außer es wird der "Full width sticky header" genutzt).
 */

export default class twtStickyMainNavigation extends Plugin {
    init() {
        if($('.nav-main').length) { // TODO

            // Init variables
            // TODO: Add announcement banner or usp-bar top
            if ($('.twt-usp-bar').hasClass('is-header')) {
                var twtStickyScrollDeep = parseInt($(".header-main").outerHeight(true) + $(".twt-usp-bar.is-header").outerHeight(true), 10);
            } else {
                var twtStickyScrollDeep = parseInt($(".header-main").outerHeight(true), 10);
            }

            var twtStickyScrollTop = parseInt($('#twt-data-attributes').attr('data-twt-sticky-scroll-top'), 10);

            var twtStickyClass = "is-sticky-nav-main";
            var twtStickyLayout = $('#twt-data-attributes').attr('data-twt-sticky-type');
            var twtStickyBreakpoint = $('#twt-data-attributes').attr('data-twt-sticky-breakpoint');
            var twtStickySearchShow = $('#twt-data-attributes').attr('data-twt-sticky-search');
            var twtStickyCartShow = $('#twt-data-attributes').attr('data-twt-sticky-cart');
            var twtStickylastScrollTop = 0;

            // Check header position for calculations
            if($(".header-main").css("position") == "absolute") {
                var twtStickyNavMainHeight = 0;
            } else {
                var twtStickyNavMainHeight = $(".nav-main").outerHeight(true);
            }

            // Calc additional boxed margin top
            if(parseInt($(".twt-boxed .body-container").css("marginTop"), 10) >= 1) {
                var twtStickyBoxedContainerMarginTop = parseInt($(".twt-boxed .body-container").css("marginTop"), 10);
            } else {
                var twtStickyBoxedContainerMarginTop = 0;
            }

            // Check if boxed layout
            if ($("body").hasClass("twt-boxed")) {
                var twtStickyForBoxed = 2;
            } else {
                var twtStickyForBoxed = 1;
            }

            // Additional scroll distance calculation
            var twtStickyScrollDeepTotal = twtStickyScrollDeep+twtStickyScrollTop+twtStickyBoxedContainerMarginTop;

            // Container-width for breakpoint
            var twtStickyBreakpointContainerWidth = parseInt($('#twt-data-attributes').attr('data-twt-sticky-container-width'), 10);

            // Check breakpoint
            if(twtStickyBreakpoint == "6") {
                var twtStickyBreakpointPixel = "992";
            } else if(twtStickyBreakpoint == "7") {
                var twtStickyBreakpointPixel = "1200";
            } else if(twtStickyBreakpoint == "8") {
                var twtStickyBreakpointPixel = twtStickyBreakpointContainerWidth;
            }

            // Scroll down
            if(twtStickyLayout == 1) {

                //  Create placeholder element to move the cart back
                if(twtStickyCartShow == 2) {
                    $("div.header-cart[data-offcanvas-cart]").parent().prepend('<span id="js-header-cart-placeholder" class="d-none"></span>');
                }
                // Create placeholder element to move the search back
                if(twtStickySearchShow == 2) {
                    $("#searchCollapse").parent().prepend('<span id="js-searchCollapse-placeholder" class="d-none"></span>');
                }

                // Show or hide
                $(window).scroll(function() {

                    if($(window).scrollTop() > twtStickyScrollDeepTotal && $(window).width() > twtStickyBreakpointPixel) {
                        $("body").addClass(twtStickyClass);

                        if(twtStickyForBoxed == 2) {
                            $(".header-main").css({'padding-bottom': twtStickyNavMainHeight + 'px'});
                        } else {
                            $("body").css({'margin-top': twtStickyNavMainHeight + 'px'});
                        }

                        // move elements
                        if(twtStickyCartShow == 2) {
                            if ($("#js-header-cart-placeholder").not("is-clone")) {
                                $("div.header-cart[data-offcanvas-cart]").appendTo($("#js-sticky-cart-position").parent()).addClass("sticky");
                                $("#js-header-cart-placeholder").addClass("is-clone");
                            }
                        }
                        if(twtStickySearchShow == 2) {
                            if ($("#js-searchCollapse-placeholder").not("is-clone")) {
                                $("#searchCollapse").appendTo($("#js-sticky-search-position").parent()).removeClass("collapse").addClass("sticky");
                                $("#js-searchCollapse-placeholder").addClass("is-clone");
                            }
                        }
                    } else{
                        $("body").removeClass(twtStickyClass);
                        if(twtStickyForBoxed == 2) {
                            $(".header-main").css({'padding-bottom':'0px'});
                        } else {
                            $("body").css({'margin-top':'0px'});
                        }

                        // Reset move elements
                        if(twtStickyCartShow == 2) {
                            if ($("#js-header-cart-placeholder").hasClass("is-clone")) {
                                $('div.header-cart[data-offcanvas-cart]').appendTo($("#js-header-cart-placeholder").parent()).removeClass("sticky");
                                $("#js-header-cart-placeholder").removeClass("is-clone");
                            }
                        }
                        if(twtStickySearchShow == 2) {
                            if ($("#js-searchCollapse-placeholder").hasClass("is-clone")) {
                                $("#searchCollapse").appendTo($("#js-searchCollapse-placeholder").parent()).addClass("collapse").removeClass("sticky");
                                $("#js-searchCollapse-placeholder").removeClass("is-clone");
                            }
                        }
                    }
                });

                // Remove sticky on resize to small breakpoint
                $(window).resize(function() {
                    if ($(window).width() < twtStickyBreakpointPixel){
                        $("body").removeClass(twtStickyClass);
                        if(twtStickyForBoxed == 2) {
                            $(".header-main").css({'margin-bottom': '0px'});
                        } else {
                            $("body").css({'margin-top':'0px'});
                        }

                        // Reset move elements
                        if(twtStickyCartShow == 2) {
                            if ($("#js-header-cart-placeholder").hasClass("is-clone")) {
                                $('div.header-cart[data-offcanvas-cart]').appendTo($("#js-header-cart-placeholder").parent()).removeClass("sticky");
                                $("#js-header-cart-placeholder").removeClass("is-clone");
                            }
                        }
                        if(twtStickySearchShow == 2) {
                            if ($("#js-searchCollapse-placeholder").hasClass("is-clone")) {
                                $("#searchCollapse").appendTo($("#js-searchCollapse-placeholder").parent()).addClass("collapse").removeClass("sticky");
                                $("#js-searchCollapse-placeholder").removeClass("is-clone");
                            }
                        }
                    }
                });
            }

            // Scroll up
            if(twtStickyLayout == 2) {

                // Sreate placeholder element to move the cart back
                if(twtStickyCartShow == 2) {
                    $("div.header-cart[data-offcanvas-cart]").parent().prepend('<span id="js-header-cart-placeholder" class="d-none"></span>');
                }
                // Create placeholder element to move the search back
                if(twtStickySearchShow == 2) {
                    $("#searchCollapse").parent().prepend('<span id="js-searchCollapse-placeholder" class="d-none"></span>');
                }

                $(window).scroll(function() {

                    // Add ready class
                    if ($(window).scrollTop() >= twtStickyScrollDeepTotal) {
                        if ($(window).width() > twtStickyBreakpointPixel){
                            $(".nav-main").addClass("ready");

                            // Move elements
                            if(twtStickyCartShow == 2) {
                                if ($("#js-header-cart-placeholder").not("is-clone")) {
                                    $("div.header-cart[data-offcanvas-cart]").appendTo($("#js-sticky-cart-position").parent()).addClass("sticky");
                                    $("#js-header-cart-placeholder").addClass("is-clone");
                                }
                            }
                            if(twtStickySearchShow == 2) {
                                if ($("#js-searchCollapse-placeholder").not("is-clone")) {
                                    $("#searchCollapse").appendTo($("#js-sticky-search-position").parent()).removeClass("collapse").addClass("sticky");
                                    $("#js-searchCollapse-placeholder").addClass("is-clone");
                                }
                            }
                        }
                    } else {
                        if(twtStickyForBoxed == 2) {
                            $(".nav-main").removeClass("ready").removeClass("show");
                            $("body").removeClass(twtStickyClass);
                            $(".header-main").css({'padding-bottom':'0px'});
                        } else {
                            $(".nav-main").removeClass("ready").removeClass("show");
                            $("body").removeClass(twtStickyClass).css({'margin-top': '0px'});
                        }

                        // Reset move elements
                        if(twtStickyCartShow == 2) {
                            if ($("#js-header-cart-placeholder").hasClass("is-clone")) {
                                $('div.header-cart[data-offcanvas-cart]').appendTo($("#js-header-cart-placeholder").parent()).removeClass("sticky");
                                $("#js-header-cart-placeholder").removeClass("is-clone");
                            }
                        }
                        if(twtStickySearchShow == 2) {
                            if ($("#js-searchCollapse-placeholder").hasClass("is-clone")) {
                                $("#searchCollapse").appendTo($("#js-searchCollapse-placeholder").parent()).addClass("collapse").removeClass("sticky");
                                $("#js-searchCollapse-placeholder").removeClass("is-clone");
                            }
                        }

                    }

                    // Show or hide
                    if ($(".nav-main").hasClass("ready")) {
                        if ($(window).scrollTop() <= twtStickyScrollDeepTotal || $(window).scrollTop() < twtStickylastScrollTop) {
                            if(twtStickyForBoxed == 2) {
                                $(".nav-main").addClass("show");
                                $("body").addClass(twtStickyClass);
                                $(".header-main").css({'padding-bottom': twtStickyNavMainHeight + 'px'});
                            } else {
                                $(".nav-main").addClass("show");
                                $("body").addClass(twtStickyClass).css({'margin-top': twtStickyNavMainHeight + 'px'});
                            }
                        } else {
                            if(twtStickyForBoxed == 2) {
                                $(".nav-main").removeClass("show");
                                $("body").removeClass(twtStickyClass);
                                $(".header-main").css({'padding-bottom':'0px'});
                            } else {
                                $(".nav-main").removeClass("show");
                                $("body").removeClass(twtStickyClass).css({'margin-top': '0px'});
                            }
                        }
                    }

                    // Save last scroll position
                    twtStickylastScrollTop = $(window).scrollTop();
                });

                // Remove ready class on resize to small breakpoint
                $(window).resize(function() {
                    if(twtStickyForBoxed == 2) {
                        $(".nav-main").removeClass("ready").removeClass("show");
                        $("body").removeClass(twtStickyClass);
                        $(".header-main").css({'padding-bottom':'0px'});
                    } else {
                        $(".nav-main").removeClass("ready").removeClass("show");
                        $("body").removeClass(twtStickyClass).css({'margin-top': '0px'});
                    }
                });
            }
        }
    }
}