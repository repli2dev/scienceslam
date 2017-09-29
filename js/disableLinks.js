$(function(){
    // Disabling all navigable things in preview
    $(document).on('click', 'a,button,input[type=submit],input[type=button]', function (e) {
        alert('Odkazy a tlačítka nejsou v náhledu funkční.');
        e.preventBubble();
        e.preventDefault();
        return false;
    });
});