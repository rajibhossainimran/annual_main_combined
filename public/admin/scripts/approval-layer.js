function getApprovalLayerSelectionElement(user_approval_role){
    let items = ['<select class="form-control" id="approval_layer_selection" required>', '<option value="">Select</option>'];
    for (const iterator of user_approval_role) {
        items.push('<option value="'+iterator.role_key+'">'+iterator.role_name+'</option>')
    }
    items.push('</select>')

    return items
}

let user_approval_role;
$.get('/user-approval-roles', function(res){
    user_approval_role = res
    const items = getApprovalLayerSelectionElement(user_approval_role)
    
    $.each($('.approval-layer-selection-container li'), function(key, val){
        const role_key = $(this).data('role-key');
        $(this).find('.selection').html(items.join(''));
        $(this).find('select').val(role_key).attr('name','approval_layers_'+$(this).parent().data('type')+'[]');
    })
})

$('.approval-layer-selection-container').on('change', '#approval_layer_selection', function(){
    const value = $(this).val()

})

$('.approval-layer-selection-container').on('click', '.approval-layer-add', function(){
    
    const items = getApprovalLayerSelectionElement(user_approval_role)

    $(this).parent().after(`
        <li>
            <div class="selection">${items.join('')}</div>
            <button type="button" class="approval-layer-add btn btn-primary">+</button>
            <button type="button" class="approval-layer-remove btn btn-danger">-</button>
        </li>
        `);

    $(this).parent().next().find('select').attr('name','approval_layers_'+$(this).parent().parent().data('type')+'[]');
    
})

$('.approval-layer-selection-container').on('click', '.approval-layer-remove', function(){
    
    $(this).parent().remove()
    
})