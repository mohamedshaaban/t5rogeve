
$( ".payment-class" ).change(function() {
    if($( this ).val()=='full')
    {
        $('.downpayment-class').parent().fadeOut();
        $('.payment-class2').parent().fadeOut();
    }
    else if($( this ).val()=='down')
    {
        $('.payment-class2').parent().fadeOut();
        $('.downpayment-class').parent().fadeIn();
    }
    else
    {
        $('.payment-class2').parent().fadeIn();
    }
});