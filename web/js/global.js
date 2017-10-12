"use strict";
/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) && $("html").addClass("ismobile"), $(document).ready(function() {
    if ($("html").hasClass("ismobile") || $(".c-overflow")[0] && function(e, t, i) {

        }(".c-overflow", "minimal-dark", "y"), $(".navigation__sub")[0] && $("body").on("click", ".navigation__sub > a", function(e) {
            e.preventDefault(), $(this).closest(".navigation__sub").toggleClass("navigation__sub--toggled"), $(this).parent().find("ul").stop().slideToggle(250)
        }), $(".top-search")[0] && ($("body").on("focus", ".top-search__input", function() {
            $(".top-search").addClass("top-search--focused")
        }), $("body").on("click", ".top-menu__trigger > a", function(e) {
            e.preventDefault(), $(".top-search").addClass("top-search--focused"), $(".top-search__input").focus()
        }), $("body").on("click", ".top-search__reset", function() {
            $(".top-search").removeClass("top-search--focused "), $(".top-search__input").val("")
        }), $("body").on("blur", ".top-search__input", function() {
            !$(this).val().length > 0 && $(".top-search").removeClass("top-search--focused")
        })), $("body").on("click", '[data-mae-target="#notifications"]', function(e) {
            e.preventDefault();
            var t = $(this).data("target");
            $("a[href=" + t + "]").tab("show")
        }), $("#widget-weather__main")[0]) {
        var e;
        $.simpleWeather({
            location: "Austin, TX",
            woeid: "",
            unit: "f",
            success: function(t) {
                var i = '<div class="widget-weather__current clearfix"><div class="widget-weather__icon widget-weather__icon-' + t.code + '"></div><div class="widget-weather__info"><h2>' + t.temp + "&deg;" + t.units.temp + '</h2><div class="widget-weather__region"><span>' + t.city + ", " + t.region + "</span><span>" + t.currently + '</span></div></div></div><ul class="widget-weather__upcoming clearfix"></ul>';
                $("#widget-weather__main").html(i), setTimeout(function() {
                    for (e = 0; e < 5; e++) {
                        var i = '<li class="media"><span class="pull-left widget-weather__icon widget-weather__icon-sm widget-weather__icon-' + t.forecast[e].code + '"></span><span class="pull-right">' + t.forecast[e].high + "/" + t.forecast[e].low + '</span><span class="media-body">' + t.forecast[e].text + "</span></li>";
                        $(".widget-weather__upcoming").append(i)
                    }
                })
            },
            error: function(e) {
                $("#widget-weather__main").html("<p>" + e + "</p>")
            }
        })
    }
    if ($(".form-group--float")[0] && ($(".form-group--float").each(function() {
            0 == !$(this).find(".form-control").val().length && $(this).addClass("form-group--active")
        }), $("body").on("blur", ".form-group--float .form-control", function() {
            var e = $(this).val(),
                t = $(this).parent();
            0 == e.length ? t.removeClass("form-group--active") : t.addClass("form-group--active")
        })), $(".collapse")[0] && ($(".collapse").on("show.bs.collapse", function(e) {
            $(this).closest(".panel").find(".panel-heading").addClass("active")
        }), $(".collapse").on("hide.bs.collapse", function(e) {
            $(this).closest(".panel").find(".panel-heading").removeClass("active")
        }), $(".collapse.in").each(function() {
            $(this).closest(".panel").find(".panel-heading").addClass("active")
        })), $(".login")[0] && $("body").on("click", ".login__block [data-block]", function(e) {
            e.preventDefault();
            var t = $(this).data("block"),
                i = $(this).closest(".login__block"),
                a = $(this).data("bg");
            i.removeClass("toggled"), setTimeout(function() {
                $(".login").attr("data-lbg", a), $(t).addClass("toggled")
            })
        }), $(".action-header__search")[0]) {
        var t;
        $("body").on("click", ".action-header__toggle", function(e) {
            e.preventDefault(), t = $(this).closest(".action-header").find(".action-header__search"), t.fadeIn(300), t.find(".action-header__input").focus()
        }), $("body").on("click", ".action-header__close", function() {
            t.fadeOut(300), setTimeout(function() {
                t.find(".action-header__input").val("")
            }, 350)
        })
    }
    $("input-mask")[0] && $(".input-mask").mask(), $(".color-picker")[0] && $(".color-picker").each(function() {
        var e = $(this).find(".color-picker__value");
        $(this).find(".color-picker__target").farbtastic(e)
    }), $(".date-time-picker")[0] && $(".date-time-picker").datetimepicker({
        icons: {
            time: "zmdi zmdi-time",
            date: "zmdi zmdi-calendar",
            up: "zmdi zmdi-chevron-up",
            down: "zmdi zmdi-chevron-down",
            previous: "zmdi zmdi-chevron-left",
            next: "zmdi zmdi-chevron-right",
            today: "zmdi zmdi-screenshot",
            clear: "zmdi zmdi-trash",
            close: "zmdi zmdi-times"
        }
    }), $(".time-picker")[0] && $(".time-picker").datetimepicker({
        format: "LT",
        icons: {
            time: "zmdi zmdi-time",
            date: "zmdi zmdi-calendar",
            up: "zmdi zmdi-chevron-up",
            down: "zmdi zmdi-chevron-down",
            previous: "zmdi zmdi-chevron-left",
            next: "zmdi zmdi-chevron-right",
            today: "zmdi zmdi-screenshot",
            clear: "zmdi zmdi-trash",
            close: "zmdi zmdi-times"
        }
    }), $(".datetime-picker-inline")[0] && $(".datetime-picker-inline").datetimepicker({
        inline: !0,
        sideBySide: !0,
        icons: {
            time: "zmdi zmdi-time",
            date: "zmdi zmdi-calendar",
            up: "zmdi zmdi-chevron-up",
            down: "zmdi zmdi-chevron-down",
            previous: "zmdi zmdi-chevron-left",
            next: "zmdi zmdi-chevron-right",
            today: "zmdi zmdi-screenshot",
            clear: "zmdi zmdi-trash",
            close: "zmdi zmdi-times"
        }
    }), $(".tab-wizard")[0] && $(".tab-wizard").bootstrapWizard({
        tabClass: "tab-wizard__nav",
        nextSelector: ".tab-wizard__next",
        previousSelector: ".tab-wizard__previous",
        firstSelector: ".tab-wizard__first",
        lastSelector: ".tab-wizard__last"
    }), $(".lightbox")[0] && $(".lightbox").lightGallery({
        enableTouch: !0
    }), $('[data-toggle="tooltip"]')[0] && $('[data-toggle="tooltip"]').tooltip(), $('[data-toggle="popover"]')[0] && $('[data-toggle="popover"]').popover(), $("html").hasClass("ie9") && $("input, textarea").placeholder({
        customClass: "ie9-placeholder"
    }), $("select.select2")[0] && $("select.select2").select2({
        dropdownAutoWidth: !0,
        width: "100%"
    }), $(".textarea-autosize")[0] && autosize($(".textarea-autosize"))
});

$(function() {
    function e(e) {
        e.requestFullscreen ? e.requestFullscreen() : e.mozRequestFullScreen ? e.mozRequestFullScreen() : e.webkitRequestFullscreen ? e.webkitRequestFullscreen() : e.msRequestFullscreen && e.msRequestFullscreen()
    }
    var t, i = $("body");
    $(this), i.on("click", "[data-mae-action]", function(a) {
        switch (a.preventDefault(), $(this).data("mae-action")) {
            case "block-open":
                t = $(this).data("mae-target"), $(t).addClass("toggled"), i.addClass("block-opened"), i.append('<div data-mae-action="block-close" data-mae-target="' + t + '" class="mae-backdrop mae-backdrop--sidebar" />');
                break;
            case "block-close":
                $(t).removeClass("toggled"), i.removeClass("block-opened"), $(".mae-backdrop--sidebar").remove();
                break;
            case "fullscreen":
                e(document.documentElement);
                break;
            case "print":
                window.print();
                break;
            case "clear-localstorage":
                swal({
                    title: "Are you sure?",
                    text: "This can not be undone!",
                    type: "warning",
                    showCancelButton: !0,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Yes, clear it",
                    cancelButtonText: "No, cancel"
                }).then(function() {
                    localStorage.clear(), swal("Cleared!", "Local storage has been successfully cleared", "success")
                })
        }
    });
    $("#application-form").dataTable({
            //Override default icon classes
        // css: {
        //     icon: 'table-bootgrid__icon zmdi',
        //     iconSearch: 'zmdi-search',
        //     iconColumns: 'zmdi-view-column',
        //     iconDown: 'zmdi-sort-amount-desc',
        //     iconRefresh: 'zmdi-refresh',
        //     iconUp: 'zmdi-sort-amount-asc',
        //     dropDownMenu: 'dropdown form-group--select',
        //     search: 'table-bootgrid__search',
        //     actions: 'table-bootgrid__actions',
        //     header: 'table-bootgrid__header',
        //     footer: 'table-bootgrid__footer',
        //     dropDownItem: 'table-bootgrid__label',
        //     table: 'table table-bootgrid',
        //     pagination: 'pagination table-bootgrid__pagination'
        // },

        // //Override default module markups
        // templates: {
        //     actionDropDown: "<span class=\"{{css.dropDownMenu}}\">" + "<a href='' data-toggle=\"dropdown\">{{ctx.content}}</a><ul class=\"{{css.dropDownMenuItems}}\" role=\"menu\"></ul></span>",
        //     search: "<div class=\"{{css.search}} form-group\"><span class=\"{{css.icon}} {{css.iconSearch}}\"></span><input type=\"text\" class=\"{{css.searchField}}\" placeholder=\"{{lbl.search}}\" /><i class='form-group__bar'></i></div>",
        //     header: "<div id=\"{{ctx.id}}\" class=\"{{css.header}}\"><p class=\"{{css.search}}\"></p><p class=\"{{css.actions}}\"></p></div>",
        //     actionDropDownCheckboxItem: "<li><div class='tabe-bootgrid__checkbox checkbox checkbox--dark'><label class=\"{{css.dropDownItem}}\"><input name=\"{{ctx.name}}\" type=\"checkbox\" value=\"1\" class=\"{{css.dropDownItemCheckbox}}\" {{ctx.checked}} /> {{ctx.label}}<i class='input-helper'></i></label></div></li>",
        //     footer: "<div id=\"{{ctx.id}}\" class=\"{{css.footer}}\"><div class=\"row\"><div class=\"col-sm-6\"><p class=\"{{css.pagination}}\"></p></div><div class=\"col-sm-6 table-bootgrid__showing hidden-xs\"><p class=\"{{css.infos}}\"></p></div></div></div>"
        // }
    });
});

$(function() {

});