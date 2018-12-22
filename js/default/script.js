/**
 *
 */
var filterTimeout;

/**
 *
 * @param {string} code
 */
function filterCode(code) {
    if (code != '') {
        let re = RegExp(code, 'i');
        $('[data-toggle=filter]').each(function(i, el) {
            el = $(el);
            el.toggleClass('d-none', !el.data('filter').match(re));
        });
    } else {
        $('[data-toggle=filter]').removeClass('d-none');
    }
}

/**
 *
 */
$(function() {
    let flash = $('#flash');

    if (flash.length) {
        setTimeout(
            function() {
                flash.fadeToggle('slow');
            },
            // Detect success or error
            flash.find('.alert-success').length ? 5000 : 15000
        );
    }

    $('[data-toggle="tooltip"]').tooltip();

    $('button[data-action=toggle]').click(function() {
        let $this = $(this);

        $.post('/api/toggle', {
            set: $this.data('set'),
            code: $this.data('code')
        }).done(function(active) {
            $('tr[data-filter=' + $this.data('code') + ']').toggleClass(
                'code-inactive',
                !active
            );
        });
    });

    $('button[data-action=delete]').click(function() {
        let $this = $(this);

        $('#deleteModal')
            .data('form', null)
            .data('set', $this.data('set'))
            .data('code', $this.data('code'))
            .data('action', $this.data('action'))
            .modal('show');
    });

    $('.confirm-delete').on('click', function(e) {
        e.preventDefault();
        $('#deleteModal')
            .data('form', $(this).closest('form'))
            .modal('show');
    });

    $('#deleteModal .confirmed').click(function() {
        let el = $('#deleteModal'),
            form = el.data('form');
        el.modal('hide');
        if (form && form.length) {
            form.submit();
        } else if (el.data('action') == 'delete') {
            let set = el.data('set'),
                code = el.data('code');
            $.post('/api/delete', { set: set, code: code }).done(function(rc) {
                if (rc > 0) {
                    $('tr[data-filter=' + code + ']').remove();
                }
            });
        }
    });

    // Edit
    $('#btn-rename').click(function(e) {
        e.preventDefault();
        $('#input-code')
            .removeClass()
            .addClass('form-control')
            .prop('readonly', '');
        $(this).hide();
    });

    $(document).on('keydown', 'textarea', function(e) {
        // Submit form on Ctrl+Enter in text area
        if ((e.keyCode == 10 || e.keyCode == 13) && e.ctrlKey) {
            $(this)
                .closest('form')
                .submit();
        }
    });

    $('#filter-code')
        // https://stackoverflow.com/a/24589806
        .on('focus', function() {
            $(this).one('click', function() {
                $(this).select();
            });
        })
        .on('keyup', function(e) {
            // Filtering
            clearTimeout(filterTimeout);
            let that = this;
            filterTimeout = setTimeout(function() {
                if (that.value != '') {
                    let re = RegExp(that.value, 'i');
                    $('[data-toggle=filter]').each(function(i, el) {
                        el = $(el);
                        el.toggleClass('d-none', !el.data('filter').match(re));
                    });
                } else {
                    $('[data-toggle=filter]').removeClass('d-none');
                }
            }, 400);
        });

    $('.g-translate').on('click', function() {
        $(this)
            .prev()
            .focus();
    });

    // Login
    $('#pw').focus();
});
