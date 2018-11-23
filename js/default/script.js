$(function() {
    setTimeout(function() {
        $('#flash').fadeOut('slow');
    }, $('#flash').hasClass('alert-success') ? 3000 : 15000);

    $('[data-toggle="tooltip"]').tooltip();

    $('.confirm-delete').on('click', function(e) {
        e.preventDefault();
        $('#deleteModal')
            .data('form', $(this).closest('form'))
            .modal('show');
    });

    $('#deleteModal .confirmed').click(function() {
        $('#deleteModal')
            .modal('hide')
            .data('form')
            .submit();
    });

    // Edit
    $(document).on('keydown', 'textarea', function(e) {
        // Submit form on Ctrl+Enter in text area
        if ((e.keyCode == 10 || e.keyCode == 13) && e.ctrlKey) {
            $(this)
                .closest('form')
                .submit();
        }
    });

    $('.g-translate').on('click', function() {
        $(this)
            .prev()
            .focus();
    });

    // Login
    $('#pw').focus();
});
