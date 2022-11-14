/*

// ピンチズーム禁止
var passiveSupported = false;
try {
    document.addEventListener("test", null, Object.defineProperty({}, "passive", {
        get: function() {
            passiveSupported = true;
        }
    }));
} catch (err) {}

document.addEventListener('touchstart', function listener(e) {
    if (e.touches && e.touches.length > 1) {
        e.preventDefault();
    }
}, passiveSupported ? { passive: false } : false);
document.addEventListener('touchmove', function listener(e) {
    if (e.touches && e.touches.length > 1) {
        e.preventDefault();
    }
}, passiveSupported ? { passive: false } : false);
*/

$(function () {
    $(".datepicker").datepicker().datepicker("setDate", "today");

    ////////////////
    // モーダル
    ////////////////
    $('.modalOpen').click(function(e) {
        const idModal = $(this).attr('id');
        if (idModal === '#modal_nippou') {
            let ledgerId = $(this).parents('article').find('input[name="ledger_id"]').val();
            let lessonAttendId = $(this).parents('article').find('input[name="lesson_attend_id"]').val();
            let commentId = $(this).parents('article').find('input[name="comment_id"]').val();
            let commentText = $(this).parents('article').find('.comment-text').text()?.trim();
            let studentNo = $(this).parents('article').find('.no').text()?.trim();
            let name = $(this).parents('article').find('.name').text()?.trim();
            let stuText = studentNo + '　' + name;

            $('#nippou').text(stuText);
            $(idModal + ' input[name="name"]').attr('value', stuText);
            $(idModal + ' input[name="ledger_id"]').attr('value', ledgerId);
            $(idModal + ' input[name="lesson_attend_id"]').attr('value', lessonAttendId);
            $(idModal + ' input[name="comment_id"]').attr('value', commentId);
            $(idModal + ' input[name="comment_text"]').removeClass('is-invalid').next().remove();
            $(idModal + ' input[name="comment_text"]').attr('value', commentText);
            $(idModal).fadeIn(300);
        } else {
            modalOpen($(this).attr('href'));
        }
        return false;
    });
    $(".modal_close").click(function () {
        //$(this).parent().parent().fadeOut(300);
        modalClose();
        return false;
    });

    //function modalOpen(id) {
    window.modalOpen = function (id) {
        $(".modal").fadeOut(300);
        $(id).fadeIn(300);
    };

    //function modalClose() {
    window.modalClose = function () {
        $(".modal").fadeOut(300);
    };

    $(".next").on("click", function () {
        var date = $(".datepicker").datepicker("getDate");
        date.setDate(date.getDate() + 1);
        $(".datepicker").datepicker("setDate", date);
    });

    $(".prev").on("click", function () {
        var date = $(".datepicker").datepicker("getDate");
        date.setDate(date.getDate() - 1);
        $(".datepicker").datepicker("setDate", date);
    });

});
