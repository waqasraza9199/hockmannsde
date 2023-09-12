/*
    _____ _                __      __
   |_   _| |_  ___ _ __  __\ \    / /_ _ _ _ ___  (R)
     | | | ' \/ -_) '  \/ -_) \/\/ / _` | '_/ -_)
     |_| |_||_\___|_|_|_\___|\_/\_/\__,_|_| \___|

    Copyright (c) TC-Innovations GmbH
    ==================================================
    Plugin "twtSlideoutCommunities"
    see Tab "Extensions" > Block "Slideout communities"
*/

import Plugin from 'src/plugin-system/plugin.class';

/* TODO
 * - use plain js...
 *
 * Prüfen:
 * - Kann das script per TWIG in den Footer integriert werden? Muss dies ein Plugin sein?
 */

/*************************************************************************
 ** ThemeWare: Slideout communities
 *************************************************************************/

/* Info:
 * Extension can be activated and configured via the theme configuration.
 *
 * DE: Das Script soll verhindern, dass das Element bei der Skalierung des Browserfenststers aus dem sichtbaren Bereich
 * rutscht!
 */

export default class twtSlideoutCommunities extends Plugin {
    init() {
        // Set element
        let el = document.getElementById("twt-slideout-communities");

        // Init variables
        let elementIsSticky = 0;

        // Add event listeners
        window.addEventListener("onload", slideoutPosition);
        //window.addEventListener("scroll", slideoutPosition);
        window.addEventListener("resize", slideoutPosition);

        // Slideout communities position function
        function slideoutPosition() {
            // Verfügbaren Abstand über dem Element berechnen.
            let windowHeight = $(window).height(); // TODO

            let elementHeight = el.offsetHeight;

            let elementSpaceAbove = windowHeight - elementHeight;
            
            // Wenn das Element den oberen Rand erreicht hat, wird die CSS-Klasse "is-sticky" hinzugefügt 
            // bzw. wieder entfernt wenn das Browser-Fenster wieder groß genug ist.
            if (elementSpaceAbove <= 1) {
                el.classList.add("is-sticky");
                elementIsSticky = 1;

            } else {
                el.classList.remove("is-sticky");
                elementIsSticky = 0;

            }
        }

    }
}