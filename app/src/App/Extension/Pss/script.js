$(function () {
    var pw = $('#pw'),
        pw1 = $('#pw1'),
        pw2 = $('#pw2');

    $('#pw, #pw1, #pw2').on('keyup', function () {
        let err =
            pw.val() == '' ||
            pw1.val() == '' ||
            pw2.val() == '' ||
            pw1.val() !== pw2.val();

        pw.toggleClass('alert-danger', pw.val() == '');
        pw1.toggleClass('alert-danger', err);
        pw2.toggleClass('alert-danger', err);
        pw1.closest('form')
            .find('button[type=submit]')
            .prop('disabled', err);
    });
});
