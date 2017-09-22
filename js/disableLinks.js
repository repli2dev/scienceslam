$(function(){
    // Disabling all navigable things in preview
    $(document).on('click', 'a,button,input[type=submit],input[type=button]', function () {
        alert('Odkazy a tlačítka nejsou v náhledu funkční.');
        return false;
    });
});