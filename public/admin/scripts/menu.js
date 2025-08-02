$("#sub_menu").css('display','none');
        $("#is_show").click(function(){
            if($(this).is(":checked")) {
               $("#sub_menu").css('display','block');
                $("#sub_menu_div").css('display','none');
            }else{
                $("#sub_menu").css('display','none');
                $("#sub_menu_div").css('display','block');
            }
        });
        $("#parent_menu").on('change', function (){
            $value=$(this).val();
            $.ajax({
                type : 'get',
                url  : '/menu/get-parent',
                data : {
                    '_token': $('input[name="_token"]').val(),
                    'id':$value
                },
                success:function(data){
                    if (jQuery.isEmptyObject(data))
                    {
                        $("#sub_menu_div").empty();
                    }else{
                        let appendText = "";
                        appendText = '<div class="form-group"><label>Existing Sub Menu </label><select class="form-control" name="sub_menu_id"><option value=""></option>';
                        data.forEach(function(item) {
                            console.log(item.show_name);
                            appendText += '<option value="'+item.id+'">'+item.show_name+'</option>';
                        });
                        appendText += '</select></div>';
                        $("#sub_menu_div").append(appendText);
                    }

                }
            });
        });