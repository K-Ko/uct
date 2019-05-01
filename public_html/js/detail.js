var detailTranslators = {
    gt: 'https://translate.google.com/#{lang}/{lang2}/{desc}',
    dt: 'https://www.deepl.com/translator#{lang}/{lang2}/{desc}'
};

/**
 *
 */
$(function() {
    $('body.detail').on('click', '[data-toggle="translate"]', function(e) {
        e.preventDefault();

        let el = $(this),
            lang = el.data('lang'),
            target = el.data('target');

        url = detailTranslators[target]
            .replace('{lang}', lang.substr(-2))
            .replace('{lang2}', el.data('lang2').substr(-2))
            .replace('{desc}', $('[name="desc[' + lang + ']"').val());

        window.open(url, target);
    });
});
