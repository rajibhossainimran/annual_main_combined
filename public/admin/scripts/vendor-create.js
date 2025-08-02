$(document).ready(function() {
    $('#org_id').change(function(event) {
        if($('#org_id').val())
            var id = $(this).val();
            $.ajax({
                url: '/api/get-suborganizations/'+id,
                type: 'get',
                dataType: 'json',
                success: function(response){
                    let items = [`<option disabled selected>Select</option>`]
                    $.each(response, function(key, val){
                        items.push(`<option value='${val.id}'>${val.name}</option>`);
                    })
                    $('#sub_org_id').html(items.join(''))
                    if(response && response.length > 0)
                        $('#sub_org_section').css('display','block')
                }
            });

    });
    $('#sub_org_id').change(function(event) {
        if($('#sub_org_id').val())
            var sub_org_id = $(this).val();
            $.ajax({
                url: '/api/get-branches/'+sub_org_id,
                type: 'get',
                dataType: 'json',
                success: function(response){
                    let items = [`<option disabled selected>Select</option>`]
                    $.each(response, function(key, val){
                        items.push(`<option value='${val.id}'>${val.name}</option>`);
                    })
                    $('#branch_id').html(items.join(''))
                    if(response && response.length > 0)
                        $('#branch_section').css('display','block')
                }
            });

    });
});