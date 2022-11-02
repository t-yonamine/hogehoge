$(function () {
    $('.delete-button').on('click', function() {
        $('#modelDelete').modal('show');
        $('#formSub').attr('action', $(this).attr('data-action'));
    });

    $('.qualification-result').on('change', function() {
        $(this).parents('.rounded').find('.qualify-selected').prop('checked', false).prop('disabled', this.value != 1);
    });
})
