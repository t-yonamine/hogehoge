$(function () { 
    $('.delete-button').on('click', function() {
        $('#modelDelete').modal('show');
        $('#formSub').attr('action', $(this).attr('data-action'));
    });
})