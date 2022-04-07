/*jslint browser: true*/ /*global jQuery*/ /*jslint this:true */ /*global window*/ /*global console*/ /*global wpColorPicker*/

(function ($) {

    "use strict";

    $(document).ready(function () {

        var func_options_framework_tabs;

        // Loads the color pickers
        $(".of-color").wpColorPicker();

        // Image Options
        $(".of-radio-img-img").on('click', function () {
            $(this).parent().parent().find(".of-radio-img-img").removeClass("of-radio-img-selected");
            $(this).addClass("of-radio-img-selected");
        });

        $(".of-radio-img-label").hide();
        $(".of-radio-img-img").show();
        $(".of-radio-img-radio").hide();

        func_options_framework_tabs = function options_framework_tabs() {

            var $group = $(".group");
            var $navtabs = $(".nav-tab-wrapper a");
            var active_tab = "";

            // Hides all the .group sections to start
            $group.hide();

            // Find if a selected tab is saved in localStorage
            if (localStorage !== "undefined") {
                active_tab = localStorage.getItem("active_tab");
            }

            // If active tab is saved and exists, load it is .group
            if (active_tab !== "" && $(active_tab).length) {
                $(active_tab).fadeIn();
                $(active_tab + "-tab").addClass("nav-tab-active");
            } else {
                $(".group:first").fadeIn();
                $(".nav-tab-wrapper a:first").addClass("nav-tab-active");
            }

            // Bind tabs clicks
            $navtabs.on('click', function (e) {

                e.preventDefault();

                // Remove active class from all tabs
                $navtabs.removeClass("nav-tab-active");

                $(this).addClass("nav-tab-active").blur();

                if (localStorage !== "undefined") {
                    localStorage.setItem("active_tab", $(this).attr("href"));
                }

                var selected = $(this).attr("href");

                $group.hide();
                $(selected).fadeIn();

            });
        };

        // Loads tabbed sections if they exist
        if ($(".nav-tab-wrapper").length > 0) {
            func_options_framework_tabs();
        }
    });
}(jQuery));