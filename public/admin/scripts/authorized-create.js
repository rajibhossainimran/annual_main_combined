$("#subOrg").on('change', function (){
    $value=$(this).val();
    $("#dept").empty();
    $.ajax({
        type : 'get',
        url  : '/settings/authorized/equipment/dept-name',
        data : {
            '_token': $('input[name="_token"]').val(),
            'id':$value
        },
        success:function(data){
            console.log(data);
            if (jQuery.isEmptyObject(data))
            {
                alert('Please Insert Department Name');
            }else{
                let appendText = "";
                appendText = '<option value="" >Select</option>';
                data.forEach(function(item) {
                    appendText += '<option value="'+item.id+'">'+item.name+'</option>';
                });
                $("#dept").append(appendText);
            }

        }
    });
});
