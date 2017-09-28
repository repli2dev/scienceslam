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

    var openPreviewCallback = function () {
        document.getElementById('preview-window').style.display = 'block';
        var body = document.querySelector('body');
        body.classList.add('no-scroll');
    };
    var closePreviewCallback = function () {
        this.parentNode.style.display = 'none';
        var temp = document.getElementsByName('preview-pane');
        if (temp.length === 1) {
            temp[0].src = 'about:blank';
        }
        var body = document.querySelector('body');
        body.classList.remove('no-scroll');
    };
    $(document).on('click', '.open-preview', openPreviewCallback);
    $(document).on('click', '.close-preview', closePreviewCallback);

    $(document).on('click', '.open-select-preview', function () {
        var selectIdSelector = $(this).attr('data-select-id');
        if (!selectIdSelector) {
            return false;
        }
        var path = $('#' + selectIdSelector).val();
        if (path) {
            var currentUrl = window.location;
            var baseUrl = currentUrl .protocol + "//" + currentUrl.host + "/";
            $('#preview-window iframe').attr('src', baseUrl + path);
            openPreviewCallback();
        }
        return false;
    });
});