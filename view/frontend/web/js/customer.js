define([
    'jquery'
],
function($){

    return function(data){
        $.ajax({
            url: data.ajaxurl,
            type:'POST',
            dataType:'json',
            data: 'ajax=1',
            success:function(response){
                if (response != null) {
                    $( "body" ).prepend( "<script>dataLayer.push({ \"customer_id\" : \""+ response + "\"})</script>");
                }
            }
        });
    }
});