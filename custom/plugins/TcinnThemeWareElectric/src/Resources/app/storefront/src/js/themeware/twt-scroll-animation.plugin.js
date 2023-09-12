/*
    _____ _                __      __
   |_   _| |_  ___ _ __  __\ \    / /_ _ _ _ ___  (R)
     | | | ' \/ -_) '  \/ -_) \/\/ / _` | '_/ -_)
     |_| |_||_\___|_|_|_\___|\_/\_/\__,_|_| \___|

    Copyright (c) TC-Innovations GmbH
    ==================================================
    Plugin "twtScrollAnimation"
    see Tab "Shopping Experiences" > Block "All types" > Section "CSS scroll animations"

*/

import Plugin from 'src/plugin-system/plugin.class';

/* TODO
 * - use plain js...
 */

/*************************************************************************
 ** ThemeWare: CMS scroll animations
 *************************************************************************/

/* Info:
 * Script can be toggled via the theme configuration.
 *
 * DE: Das Script prüft die Sichtbarkeit eines CMS-Elementes und setzt eine entsprechende CSS-Klasse für die eigentliche
 * CSS-Animation.
 */

export default class twtScrollAnimation extends Plugin {
    init() {

        // Define animation variables
        var twtCmsAnimationRepeating = false; // Define if animation should be repeated when cms block already was visible > true/false
        var twtCmsAnimationElementFullyVisible = false; // Set if cms block should be animated whether it is visible completely > true/false

        function twtCmsAnimate() {
            $('.twt-cms-animation').each(function () {
                // Init variables
                var element = this;
                var position = element.getBoundingClientRect();

                if(twtCmsAnimationElementFullyVisible) {
                    // Element is completely visible
                    if(position.top >= 0 && position.bottom <= window.innerHeight) {
                        $(element).addClass('is-visible');

                    } else {
                        // Check if animation should be repeated
                        if(twtCmsAnimationRepeating) {
                            $(element).removeClass('is-visible');
                        }
                    }

                } else {
                    // Element is not completely visible
                    if(position.top < window.innerHeight && position.bottom >= 0) {
                        $(element).addClass('is-visible');

                    } else {
                        // Check if animation should be repeated
                        if(twtCmsAnimationRepeating) {
                            $(element).removeClass('is-visible');
                        }
                    }
                }
            });
        }

        window.addEventListener('load', twtCmsAnimate);
        window.addEventListener('scroll', twtCmsAnimate);
    }
}


