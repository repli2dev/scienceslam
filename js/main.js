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
});