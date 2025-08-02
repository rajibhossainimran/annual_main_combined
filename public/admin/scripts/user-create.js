function nospaces(t) {
    if (t.value.match(/\s/g)) {
        t.value = t.value.replace(/\s/g, "");
    }
}
$("#sub_org_section").css("display", "none");
$(document).ready(function () {
    var id = $("#org_id").val();
    $.ajax({
        url: "/api/get-suborganizations/" + id,
        type: "get",
        dataType: "json",
        success: function (response) {
            let items = [`<option disabled selected>Select</option>`];
            $.each(response, function (key, val) {
                items.push(`<option value='${val.id}'>${val.name}</option>`);
            });
            $("#sub_org_id").html(items.join(""));
            if (response && response.length > 0)
                $("#sub_org_section").css("display", "block");
        },
    });

    $("#branch_section").css("display", "none");
    $("#sub_org_id").change(function (event) {
        if ($("#sub_org_id").val()) {
            var sub_org_id = $(this).val();
            $.ajax({
                url: "/api/get-branches/" + sub_org_id,
                type: "get",
                dataType: "json",
                success: function (response) {
                    let items = [`<option disabled selected>Select</option>`];
                    $.each(response, function (key, val) {
                        items.push(
                            `<option value='${val.id}'>${val.name}</option>`
                        );
                    });
                    $("#branch_id").html(items.join(""));
                    if (response && response.length > 0)
                        $("#branch_section").css("display", "block");
                },
            });

            $.ajax({
                url: "/wings-by-org/" + sub_org_id,
                type: "get",
                dataType: "json",
                success: function (response) {
                    let items = [`<option value="">Select Wing</option>`];
                    $.each(response, function (key, val) {
                        items.push(
                            `<option value='${val.id}'>${val.name}</option>`
                        );
                    });
                    $("#wing_id").html(items.join("")).show();
                },
            });

            if ($("#user_approval_role_id").val() == 22) {
                $("#wing_form").removeClass("dis-none");
                $("#wing_id").attr("required", "required");
            }
        } else {
            $("#wing_form").addClass("dis-none");
            $("#wing_id").removeAttr("required");
        }
    });

    $("#user_approval_role_id").change(function () {
        const approval_role = $(this).val();

        if (approval_role == 22 && $("#sub_org_id").val()) {
            $("#wing_form").removeClass("dis-none");
            $("#wing_id").attr("required", "required");
        } else {
            $("#wing_form").addClass("dis-none");
            $("#wing_id").removeAttr("required");
        }
    });
});
