/*
    _____ _                __      __
   |_   _| |_  ___ _ __  __\ \    / /_ _ _ _ ___  (R)
     | | | ' \/ -_) '  \/ -_) \/\/ / _` | '_/ -_)
     |_| |_||_\___|_|_|_\___|\_/\_/\__,_|_| \___|

    Copyright (c) TC-Innovations GmbH
    ==================================================
    Plugin "twtFloatingWidget"
    see Tab "Extensions" > Block "Floating widget"

*/

import Plugin from 'src/plugin-system/plugin.class';
import BackdropUtil from 'src/utility/backdrop/backdrop.util';

/* TODO
 * - Add resize function...
 * - Improve onload as element is not positioned sticky when window is too small...
 *
 * Prüfen:
 * - Kann das script per TWIG in den Footer integriert werden? Muss dies ein Plugin sein?
 */

/*************************************************************************
 ** ThemeWare: Floating slideout widget
 *************************************************************************/

/* Info:
 * Extension can be activated and configured via the theme configuration.
 *
 * DE: Das Widget kann am rechten oder linken Fensterrand angezeigt werden. Größe und Position können vom User
 * konfiguriert werden. Das Script überwacht das "Öffnen/Schließen" und ergänzt (je nach Konfiguration) einen Backdrop.
 * Zudem soll das Script verhindern, dass das Widget bei der Skalierung des Browserfenststers aus dem sichtbaren
 * Bereich rutscht!
 */

export default class twtFloatingWidget extends Plugin {
    init() {
        // Set element
        let el = document.getElementById("twt-floating-widget");

        // Init variables
        let elementHeight = el.offsetHeight;
        let elementTransform = 0;
        
        if (window.getComputedStyle(el,null).getPropertyValue("transform") !== 'none') {
            elementTransform = elementHeight / 2;
        }

        let isSticky = 0;

        let minWindowHeight = 0;
        let minWindowWidth = 768; // Widget is hidden below this value

        let twtBackdropShow;

        // Check if backdrop usage is configured
        twtBackdropShow = el.getAttribute('data-twt-floating-widget-backdrop') === 'true';
        /*if (twtFloatingWidgetBackdrop === 'true') {
            twtBackdropShow = true;
        } else {
            twtBackdropShow = false;
        }*/

        // Add event listeners
        window.addEventListener("onload", widgetPosition);
        // window.addEventListener("scroll", widgetPosition);
        window.addEventListener("resize", widgetPosition);


        /* --- Floating widget position --- */

        function widgetPosition() {
            // Init variables
            let windowHeight = $(window).height(); // TODO

            // Verfügbaren Abstand für das Element berechnen. (per CSS 'bottom' positioniert)
            let elementTopSpace = el.offsetTop + elementTransform;
            let elementBottomSpace = windowHeight - el.offsetTop - elementHeight - elementTransform;

            if ($(window).width() >= minWindowWidth) { // Avoid changes when hidden (< 768px)
                if (isSticky === 0) { // Element is not sticky...
                    if (elementTopSpace <= 1) {
                        // Add sticky classes if the element has reached to top edge of the window
                        el.classList.add("is-sticky");
                        el.classList.add("top");

                        isSticky = 1;
                        minWindowHeight = windowHeight;

                    } /*else if (elementBottomSpace <= 1) {
                        // Add sticky classes if the element has reached to bottom edge of the window (Not needed for now)
                        el.classList.add("is-sticky");
                        el.classList.add("bottom");

                        isSticky = 2;
                        minWindowHeight = windowHeight;
                    }*/

                } else { // Element is sticky...
                    if (isSticky === 1 && minWindowHeight > 0 && windowHeight > minWindowHeight) {
                        el.classList.remove("is-sticky");
                        el.classList.remove("top");

                        isSticky = 0;
                        minWindowHeight = 0;
                    }

                    // Not needed for now
                    /*if (isSticky === 2 && minWindowHeight > 0 && windowHeight > minWindowHeight) {
                        el.classList.remove("is-sticky");
                        el.classList.remove("bottom");

                        isSticky = 0;
                        minWindowHeight = 0;
                    }*/

                }
            }

            // Close in mobile viewports
            if ($(window).width() < minWindowWidth) { // TODO
                closeWidget();
            }

        }


        /* --- Click tab function --- */

        // Init variables
        let button = document.getElementById('twt-floating-widget').querySelector('.twt-floating-widget-title');

        button.addEventListener('click', function(){
            if (el.getAttribute('data-twt-floating-widget-open') === 'true') {
                closeWidget();

            } else {
                // Set status 'open' to 'true'
                el.setAttribute('data-twt-floating-widget-open', 'true');

                // Show backdrop
                if (twtBackdropShow) {
                    BackdropUtil.create();
                }

                // Backdrop clicked
                $(".modal-backdrop").click(function () { // TODO
                    closeWidget();
                });
            }
        });


        /* --- Close widget function --- */

        function closeWidget() {
            // Set status 'open' to 'false'
            el.setAttribute('data-twt-floating-widget-open', 'false');

            // Remove backdrop
            if (twtBackdropShow) {
                BackdropUtil.remove();
            }
        }

    }
}