jQuery(document).ready(function($){
    document.formvalidator.setHandler('custom', function (value) {
       regex=/^[^0-9]+$/;
            return regex.test(value);
    });
});
