/**
 * Created by jimmy on 24/07/17.
 */

$(function () {
    var startDate = $('#date-start'),
        endDate = $('#end-date'),
        dayType = $('#day-type'),
        dayLongGroup = $('#day-long-group'),
        dayLeft = $('#day-left'),
        regDayLong = $('input[type=radio][name=day-long]'),
        description = $('#description');

    validateState();

    $('.picker').datetimepicker({
        format: 'DD-MM-YYYY'
    });

    dayType.change(function () {
        validateState();
    });

    regDayLong.change(function () {
        var endDate = $('#end-date');
        $('#time-group').removeClass('hide');
    });

    function validateState() {
        if (dayType.val() == 0) { // VALIDATE OPTION FOR REGULAR
            dayLongGroup
                .add(dayLeft)
                .add(description)
                .removeClass('hide');

        } else {
            dayLongGroup
                .add(dayLeft)
                .add(description)
                .addClass('hide');
        }
    }
});
