(function ($) {
    "use strict";

    /**
     * Calculates the total scroll distance in pixels.
     *
     * @returns number
     */
    function get_actual_scroll_distance() {
        return $(document).innerHeight() - window.innerHeight;
    }

    /**
     * Calculates the current percentage of the page that has been scrolled.
     *
     * Returns a value from 0 to 1
     *
     * @returns number
     */
    function get_current_scroll_percentage() {
        return window.pageYOffset / get_actual_scroll_distance();
    }

    /**
     * Calculates the scroll add-age. This is a ratio of current scroll percentage against screen height.
     *
     * This is used so that when the scroll bar is at 0% no add-age is returned, when the scroll bar is
     * at 100% the return will be the full height of the screen.
     *
     * @returns number Pixels of adjustment needed for accurate scroll positioning.
     */
    function get_current_scroll_addage() {
        return window.innerHeight * get_current_scroll_percentage();
    }

    /**
     * Calculates & returns the current detector position. If location is set then
     * the returned value will include an adjustment based on point used.
     *
     * @param point (string) Location of the detector.
     *
     * @returns number detector_position - Returns the current detector position in px.
     */
    function get_current_detector_position(point) {
        var detector_position = window.pageYOffset;

        if (point !== undefined) {
            switch (point) {
            case 'floating':
                detector_position += get_current_scroll_addage();
                break;
            case 'bottom':
                detector_position += window.innerHeight;
                break;
            }
        }

        return detector_position;
    }

    $.fn.popmake = $.fn.popmake || {};
    $.fn.popmake.triggers = $.fn.popmake.triggers || {};

    $.extend($.fn.popmake.triggers, {
        scroll: function (settings) {
            var $popup = PUM.getPopup(this),
                $window = $(window),
                popupID = PUM.getSetting($popup, 'id'),
                eventID = 'scroll.pum-stp-' + popupID,
                distance, distanceUnit,
                trigger_point = 'top',
                trigger_distance, trigger_element,
                scroll_trigger_open, scroll_trigger_close;

            // Merge Defaults.
            settings = $.extend({
                trigger_type: 'distance',
                distance: '75%',
                element_point: 'e_top-s_bottom',
                element_type: 'shortcode',
                element_selector: '',
                close_on_up: false
            }, settings);

            switch (settings.trigger_type) {
            case 'distance':
                distanceUnit = settings.distance.replace(/[0-9]/g, '');
                distance = settings.distance.replace(distanceUnit, '');

                // Check the unit passed, convert all to a px value for comparison.
                switch (distanceUnit) {
                case "px":
                    trigger_distance = distance;
                    break;
                case "rem":
                    trigger_distance = Number(getComputedStyle(document.body, "").fontSize.match(/(\d*(\.\d*)?)px/)[1]) * distance;
                    break;
                case "%":
                    // Set the max/min of user submitted values.
                    distance = distance >= 0 && distance <= 100 ? distance : distance <= 0 ? 0 : 100;
                    trigger_distance = (distance / 100) * $(document).innerHeight();
                    trigger_point = 'floating';
                    break;
                }
                break;

            case 'element':
                trigger_element = settings.element_type === 'css_selector' ? $(settings.element_selector).eq(0) : $("#scroll_pop_trigger-" + popupID + ", .pum-stp-trigger-" + popupID).eq(0);
                trigger_point = settings.element_point.indexOf('s_top') >= 0 ? 'top' : 'bottom';
                break;
            }

            /**
             * Assign the scroll open trigger function. This will be called
             * during scrolling to check if the popup should be triggered.
             */
            scroll_trigger_open = function () {
                // If the popup is already open, Cookie exists or conditions fail return.
                if ($popup.popmake('state', 'isOpen') || $popup.popmake('checkCookies', settings) || !$popup.popmake('checkConditions')) {
                    return;
                }

                if (trigger_element && trigger_element.length) {
                    trigger_distance = settings.element_point.indexOf('e_top') >= 0 ? trigger_element.offset().top : trigger_element.offset().top + trigger_element.outerHeight();
                }

                // If current adjusted scroll position is more than the trigger.
                if (get_current_detector_position(trigger_point) >= trigger_distance) {

                    // Turn of scroll_trigger_open checking on page scroll.
                    $window.off(eventID + '-open');

                    // Assign last_open_trigger global value.
                    $.fn.popmake.last_open_trigger = 'Scroll Trigger - Type: ' + settings.trigger_type;

                    // Open the popup.
                    $popup.popmake('open', function () {

                        // If close on up is enabled hook the scroll_trigger_close event to the scroll event.
                        if (settings.close_on_up) {
                            $window.on(eventID + '-close', scroll_trigger_close);
                        }

                        // Disable analytics tracking events so that they are only counted one time.
                        $popup.off('popmakeBeforeOpen.analytics');
                    });
                }
            };

            /**
             * Assign the scroll close trigger function. This will be called
             * during scrolling to check if the popup should be closed.
             */
            scroll_trigger_close = function () {

                // If the popup is not open then return.
                if (!$popup.popmake('state', 'isOpen')) {
                    return;
                }

                if (trigger_element && trigger_element.length) {
                    trigger_distance = settings.element_point.indexOf('e_top') >= 0 ? trigger_element.offset().top : trigger_element.offset().top + trigger_element.outerHeight();
                }

                // If current adjusted scroll position is less than the trigger.
                if (get_current_detector_position(trigger_point) < trigger_distance) {

                    // Turn of scroll_trigger_close checking on page scroll.
                    $window.off(eventID + '-close');

                    // Hook the scroll_trigger_open event to the window scroll event.
                    $window.on(eventID + '-open', scroll_trigger_open);

                    // Assign last_close_trigger global value.
                    $.fn.popmake.last_close_trigger = 'Scroll Trigger - Close On Scroll Up';

                    // Close the popup.
                    $popup.popmake('close');

                    // Disable analytics tracking events so that they are only counted one time.
                    $popup.off('popmakeBeforeClose.analytics');
                }
            };

            // Hook the scroll_trigger_open event to the window scroll event.
            $window.on(eventID + '-open', scroll_trigger_open);
        }
    });
}(window.jQuery));