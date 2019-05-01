$(function () {
    // Toggle button <> inputs
    $('.table').on('click', '[data-user]', function () {
        let cell = $(this).closest('td');
        cell.children().toggleClass('d-none');
    });

    // Toggle button <> inputs back on ESC
    $('.table').on('keyup', 'input', function (e) {
        if (e.keyCode == 27) {
            // ESC
            let cell = $(this).closest('td');
            cell.find('input[type=password]').val('');
            cell.children().toggleClass('d-none');
        }
    });
});
