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


$(function() {


    $(".datepicker").datepicker().datepicker('setDate', 'today');

    ////////////////
    // モーダル
    ////////////////
    $('.modalOpen').click(function(e) {
        modalOpen($(this).attr('href'));
        return false;
    });
    $('.modal_close').click(function() {
        //$(this).parent().parent().fadeOut(300);
        modalClose();
        return false;
    });

    //function modalOpen(id) {
    window.modalOpen = function(id) {
        $('.modal').fadeOut(300);
        $(id).fadeIn(300);
    }

    //function modalClose() {
    window.modalClose = function() {
        $('.modal').fadeOut(300);
    }



});