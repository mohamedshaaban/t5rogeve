
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


    $('.notificationfor-class').on('change', function() {
        if($( this ).val()==0)
        {
            $('.notificationevent-class').parent().fadeOut();
            $('.notificationuser-class').parent().fadeOut();
            $('.notificationfamily_name-class').parent().fadeOut();
            $('.notificationfather_name').parent().fadeOut();
            $('.notificationgrandfather_name').parent().fadeOut();
            $('.notificationfull_name-class').parent().fadeOut();

        }
        else if($( this ).val()==1)
        {
            $('.notificationevent-class').parent().fadeIn();
            $('.notificationuser-class').parent().fadeOut();
            $('.notificationfamily_name-class').parent().fadeOut();
            $('.notificationfather_name').parent().fadeOut();
            $('.notificationgrandfather_name').parent().fadeOut();
            $('.notificationfull_name-class').parent().fadeOut();

        }
        else
        {
            $('.notificationevent-class').parent().fadeOut();
            $('.notificationuser-class').parent().fadeIn();
            $('.notificationfamily_name-class').parent().fadeIn();
            $('.notificationfather_name').parent().fadeIn();
            $('.notificationgrandfather_name').parent().fadeIn();
            $('.notificationfull_name-class').parent().fadeIn();

        }
    });


    $('.event-class').on('change', function() {

    });

    $('.filterevent-class').on('change', function() {
        var data = $(".filterevent-class option:selected").text();
        $.ajax({
            type: "GET",
            url: "/admin/fetch/eventdashdetails/"+$( this ).val(),
            success: function(response){
                //if request if made successfully then the response represent the data
                $('#eveDetAmtRem').text(response.eveDetAmtRem);
                $('#eveDetBookSeats').text(response.eveDetBookSeats);
                $('#eveDetAmtFull').text(response.eveDetAmtFull);
                $('#eveDetAmtDwnPay').text(response.eveDetAmtDwnPay);
                $('#eveDetAmtRem').text(response.eveDetAmtRem);
                $('#eveDetRegUser').text(response.eveDetRegUser);


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

    //NotiFICATION paGE
    $('.notificationuser-class').on('change', function() {
        var data = $(".notificationuser-class option:selected").text();

        $.ajax({
            type: "GET",
            url: "/admin/fetch/studentdetails/"+$( this ).val(),
            success: function(response){
                //if request if made successfully then the response represent the data
                $('.notificationfamily_name-class').val(response.family_name);
                $('.notificationfather_name').val(response.father_name);
                $('.notificationgrandfather_name').val(response.grandfather_name);
                $('.notificationfull_name-class').val(response.full_name);

            }
        });

    })
});
