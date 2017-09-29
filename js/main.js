$(function(){
    // Hiding of irrelevant fieldset depending on block layout
    $(document).on('change', 'select[name=layout]', function () {
        var value = this.options[this.selectedIndex].value;
        $('span[class^=\'layout-\']').closest('fieldset').each(function () {
            if ($(this).find('span[class=\'layout-' + value + '\']').length === 1) {
                w3.showElement(this);
            } else {
                w3.hideElement(this);
            }
        })
    });
    $('select[name=layout]').change();

    $(document).on('click', '.open-select-preview', function () {
        var selectIdSelector = $(this).attr('data-select-id');
        if (!selectIdSelector) {
            return false;
        }
        var path = $('#' + selectIdSelector).val();
        if (path) {
            var currentUrl = window.location;
            var baseUrl = currentUrl .protocol + "//" + currentUrl.host + "/";
            $.magnificPopup.open({
                items: {
                    src: baseUrl + path,
                    type: 'iframe'
                }
            });
        }
        return false;
    });
    $.extend(true, $.magnificPopup.defaults, {
        tClose: 'Zavřít',
        tLoading: 'Načítání...',
        gallery: {
            tPrev: 'Předchozí',
            tNext: 'Následující',
            tCounter: '%curr%/%total%'
        },
        image: {
            tError: '<a href="%url%">Obrázek</a> se nepodařilo načíst.'
        },
        ajax: {
            tError: '<a href="%url%">Obsah</a> se nepodařilo načíst.'
        }
    });
    $('.new-gallery').magnificPopup({
        delegate: 'a',
        type: 'image',
        gallery: {enabled: true}
    });
    $('.generic-preview').magnificPopup({
        type: 'iframe',
        iframe: {
            markup: '<div class="mfp-iframe-scaler">'+
            '<div class="mfp-close"></div>'+
            '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
            '</div>', // HTML markup of popup, `mfp-close` will be replaced by the close button

        }
    });
});