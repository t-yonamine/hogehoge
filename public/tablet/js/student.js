$(function() {


    resized(null);
    $(window).resize(resized);

    function resized(e) {
        $('#student_data_area').height(document.documentElement.clientHeight - 440);
    }

});