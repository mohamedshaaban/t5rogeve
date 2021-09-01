
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
$(function(){


    $('.event-class').on('change', function() {
        var data = $(".event-class option:selected").text();
        $.ajax({
            type: "GET",
            url: "/admin/fetch/eventdetails/"+$( this ).val(),
            success: function(response){
                //if request if made successfully then the response represent the data
                $('.freeseats-class').val(response.free_seats);
                $('.eventprice-class').val(response.price);
                $('.downpayment-class').val(response.minimum_downpayment_amount);
                $('.payment-class2').val(response.downpayment_amount2);

            }
        });
    })
    $('.student-class').on('change', function() {
        var data = $(".student-class option:selected").text();

        $.ajax({
            type: "GET",
            url: "/admin/fetch/studentdetails/"+$( this ).val(),
            success: function(response){
                //if request if made successfully then the response represent the data
                $('.booking-full_name').val(response.full_name);
                $('.booking-father_name').val(response.father_name);
                $('.booking-grandfather_name').val(response.grandfather_name);
                $('.booking-family_name').val(response.family_name);

            }
        });

    })
});
