window.app_url = "";
window.asset_url = "/storage";
window.demand_index_url = "/demand";
window.issue_order_direct_index_url = "/issue-order-direct";
window.pvmsStockAddUrl = "/pvms-stock-add";
window.pvmsStockDelUrl = "/pvms-stock-del";
window.annual_demand_index_url = "/annual_demand";
window.notesheet_create_url = "/notesheet/create";
window.note_sheet_url = "/notesheet";
window.tender_url = "/tender";
window.cover_letter = "/csr-cover-letter";

// for search box
$(".default_option").click(function () {
    $(".dropdown_search ul").toggleClass("active");
});

$(".dropdown_search ul li").click(function () {
    var text = $(this).text();
    $(".default_option").text(text);
    $(".dropdown_search ul").removeClass("active");
});
$("#searchButton").click(function () {
    $("#searchform").submit();
});

$(".dropdown_search li").click(function () {
    $(".type_search").val($(this).text());
    // alert($(this).text()); // gets text contents of clicked li
});
$(".close").click(function () {
    $(this).parent().closest(".show").remove();
});
// end
//search box

$(".showDropdown").keyup(function () {
    var inputField = document.getElementById("inputField");
    var dropdownMenu = document.getElementById("dropdownMenu");
    var inputValue = inputField.value.toLowerCase();
    dropdownMenu.innerHTML = ""; // Clear previous results

    if (inputValue.length > 0) {
        $.get(
            window.app_url + "/settings/pvms/search?keyword=" + inputValue,
            function (res) {
                res.forEach(function (item) {
                    // console.log(item.pvms_id);
                    // if (item.toLowerCase().indexOf(inputValue) !== -1) {
                    var option = document.createElement("a");
                    option.textContent = item.nomenclature;
                    option.onclick = function () {
                        selectItem(item.nomenclature, item.id);
                    };
                    dropdownMenu.appendChild(option);
                    // }
                });
                dropdownMenu.classList.add("show2");
            }
        );
    } else {
        dropdownMenu.classList.remove("show2");
    }
});
function selectItem(item, id) {
    $(".showDropdown").val(item);
    $(".showDropdownHide").val(id);
    // var selectedItemDiv = document.getElementById("selectedItem");
    // selectedItemDiv.textContent = "Selected: " + item;
    // var dropdownMenu = document.getElementById("dropdownMenu");
    dropdownMenu.classList.remove("show2");
}

$(".showDropdownPVMS").keyup(function () {
    var inputField = document.getElementById("inputField");
    var dropdownMenu = document.getElementById("dropdownMenu");
    var inputValue = inputField.value.toLowerCase();
    dropdownMenu.innerHTML = ""; // Clear previous results

    if (inputValue.length > 0) {
        $.get(
            window.app_url + "/settings/pvms/search?keyword=" + inputValue,
            function (res) {
                res.forEach(function (item) {
                    // console.log(item.pvms_id);
                    // if (item.toLowerCase().indexOf(inputValue) !== -1) {
                    var option = document.createElement("a");
                    option.textContent = item.nomenclature;
                    option.onclick = function () {
                        selectItem2(item.nomenclature, item.id);
                    };
                    dropdownMenu.appendChild(option);
                    // }
                });
                dropdownMenu.classList.add("show2");
            }
        );
    } else {
        dropdownMenu.classList.remove("show2");
    }
});

// Patient file
$("#patientfile").on("click", function () {
    var value = $(this).attr("data-id");
    // console.log($('input[name="_token"]').val());
    $.ajax({
        type: "get",
        url: "/demand/download/pdf-attached",
        data: {
            _token: $('input[name="_token"]').val(),
            id: value,
        },
        dataType: "json",
        success: function (data) {
            if (data.length > 0) {
                data.forEach(function (item, key) {
                    if (key === 0) {
                        window.open(
                            window.app_url +
                                "/demand/pdf-attached/open/" +
                                item.id,
                            `_blank_first_${key.toString()}`
                        );
                    } else {
                        setTimeout(function () {
                            window.open(
                                window.app_url +
                                    "/demand/pdf-attached/open/" +
                                    item.id,
                                "_blank_${key.toString()}"
                            );
                        }, 1500 * key);
                    }
                });
            }
        },
    });
});

// end

function selectItem2(item, id) {
    var selectedItemDiv = document.getElementById("selectedItem");
    selectedItemDiv.textContent = "Selected PVMS";
    var dropdownMenu = document.getElementById("dropdownMenu");
    dropdownMenu.classList.remove("show2");
    $("#inputField").val("");

    let items = [];
    items.push(`
                <tr>
                    <td width="60%">${item}
                    <input type="hidden" class="pvms" name="pvms_id[]" value="${id}">
                    </td>
                    <td width="30%"><input type="number" step="any" required name="price[]" class="form-control price"></td>
                    <td>
                            <button type="button" class="btn btn-outline-danger border-0 removePVMS">
                                <i class="fa fa-trash-alt"></i>
                            </button>

                    </td>
                </tr>
                `);

    $("#pvms-tbody").append(items.join(""));
}
$("#pvms-tbody").on("click", ".removePVMS", function () {
    $(this).parent().parent().remove();
});

$("#idForm").submit(function (e) {
    e.preventDefault(); // avoid to execute the actual submit of the form.
});
$(document).on("click", "#submit-btn", function (event) {
    event.preventDefault();

    var pvms = [];
    var price = [];
    $(".pvms").each(function () {
        pvms.push($(this).val());
    });
    $(".price").each(function () {
        price.push($(this).val());
    });
    // console.log(pvms);
    $.ajax({
        type: "post",
        url: window.app_url + "/settings/store/rate-running-pvms",
        data: {
            _token: $('input[name="_token"]').val(),
            supplier: $("#supplier").val(),
            start_date: $(".strd").val(),
            end_date: $(".endd").val(),
            tender_ser_no: $(".tender").val(),
            price: price,
            pvms_id: pvms,
        },
        success: function (data) {
            // console.log(data);
            if (data === "Insert Data") {
                window.location.href =
                    window.app_url + "/settings/rate-running-pvms";
            }
        },
    });

    // $( "#pvmsForm" ).submit();
});
//end
// data table
// let table = new DataTable('#myTable', {
//     responsive: true
// });
$(".check-all").click(function () {
    $("input:checkbox").not(this).prop("checked", this.checked);
});

$.get("/get-loged-user-approval-role", function (res) {
    window.user_approval_role = res;
});

$(document).ready(function () {
    $.get("/dashboard-demand-notesheet-csr", function (res) {
        if (res.demand_count > 0) {
            $("#pending_demand_count").html(
                `<div class="pl-2"><span class="badge badge-warning">${res.demand_count}</span></div>`
            );
        } else if (res.notesheet_count > 0) {
            $("#pending_notesheet_count").html(
                `<div class="pl-2"><span class="badge badge-warning">${res.notesheet_count}</span></div>`
            );
        } else if (res.csr_count > 0) {
            $("#pending_csr_count").html(
                `<div class="pl-2"><span class="badge badge-warning">${res.csr_count}</span></div>`
            );
        }
    });

    $("#example").DataTable();
    $.fn.dataTable.ext.type.order["date-custom-pre"] = function (d) {
        return moment(d, "D MMM YYYY").unix(); // Convert date to a Unix timestamp for sorting
    };
    $("#work_order_table").DataTable({
        columnDefs: [
            {
                targets: 4, // Target the date column
                type: "date-custom", // Use the custom date sorting function
            },
        ],
        order: [[4, "desc"]],
    });
    var stockPvmsListTable = $("#stockPvmsListTable").DataTable({
        // "bFilter": false,
        // "bLengthChange": false,
        processing: true,
        serverSide: true,
        pageLength: 25,
        ordering: false,
        pagingType: "simple_numbers",
        ajax: {
            url: "/report/pvms-stock-list-api",
            data: function (d) {
                d.length = $("#stockPvmsListTable").DataTable().page.len();
                d.start = $("#stockPvmsListTable")
                    .DataTable()
                    .page.info().start;
                d.search = $('input[type="search"]').val();
                d.sub_org_id = $("#sub_org_id").val();
                d.pvms_item_type = $("#pvms_item_type").val();
                d.date_range = $('input[name="datefilter"]').val();
            },
        },
        // language: {
        //     paginate: {
        //         previous: '<button id="prev-btn" class="btn btn-sm btn-primary">Previous</button>',
        //         next: '<button id="next-btn" class="btn btn-sm btn-primary px-4">Next</button>'
        //     }
        // },
        columns: [
            {
                data: "null",
                name: "Sl",
                render: function (data, type, row, index) {
                    return "";
                },
            },
            { data: "pvms_id" },
            { data: "nomenclature" },
            {
                data: "null",
                name: "unit_name",
                render: function (data, type, row) {
                    return row.unit_name ? row.unit_name.name : "";
                },
            },
            {
                data: "null",
                name: "specification_name",
                render: function (data, type, row) {
                    return row.specification_name
                        ? row.specification_name.name
                        : "";
                },
            },
            {
                data: "null",
                name: "item_group_name",
                render: function (data, type, row) {
                    return row.item_group_name ? row.item_group_name.name : "";
                },
            },

            {
                data: "null",
                name: "itemSectionName",
                render: function (data, type, row) {
                    return row.item_section_name
                        ? row.item_section_name.name
                        : "";
                },
            },
            {
                data: "null",
                name: "item_typename",
                render: function (data, type, row) {
                    return row.item_typename ? row.item_typename.name : "";
                },
            },
            {
                data: "null",
                name: "stock_qty",
                render: function (data, type, row, index) {
                    return row.stock_qty ? row.stock_qty : 0;
                },
            },
            {
                data: "null",
                name: "action",
                render: function (data, type, row) {
                    return `
                    <div class="text-center">
                        <a href="/report/pvms-stock/${row.id}">
                            <i class="fa fa-eye"></i>
                            <br /> View
                        </a>
                    </div>
                    `;
                },
            },
        ],
        rowCallback: function (row, data, index) {
            $("td:eq(0)", row).html(index + 1); // Incremental numbering starting from 1
        },
    });

    $("#sub_org_id").on("change", function () {
        stockPvmsListTable.draw();
    });
    $("#pvms_item_type").on("change", function () {
        stockPvmsListTable.draw();
    });

    $('input[name="datefilter"]').on(
        "apply.daterangepicker",
        function (ev, picker) {
            $(this).val(
                picker.startDate.format("DD/MM/YYYY") +
                    " - " +
                    picker.endDate.format("DD/MM/YYYY")
            );
            stockPvmsListTable.draw();
        }
    );

    $('input[name="datefilter"]').on(
        "cancel.daterangepicker",
        function (ev, picker) {
            $(this).val("");
            stockPvmsListTable.draw();
        }
    );

    var consilidatedAnnualDemand = $("#consilidatedAnnualDemand").DataTable({
        // "bFilter": false,
        // "bLengthChange": false,
        processing: true,
        serverSide: true,
        pageLength: 25,
        ordering: false,
        pagingType: "simple_numbers",
        ajax: {
            url: "/report/annual-demand-unit/json",
            data: function (d) {
                d.length = $("#consilidatedAnnualDemand")
                    .DataTable()
                    .page.len();
                d.start = $("#consilidatedAnnualDemand")
                    .DataTable()
                    .page.info().start;
                // d.search = $('input[type="search"]').val();
                d.financial_year = $("#year_id").val();
            },
        },
        columns: [
            {
                data: "null",
                name: "Sl",
                render: function (data, type, row, index) {
                    return "";
                },
            },
            {
                data: "null",
                name: "Pvms Id",
                render: function (data, type, row, index) {
                    return row.pvms ? row?.pvms?.pvms_id : "";
                },
            },
            {
                data: "null",
                name: "Pvms Id",
                render: function (data, type, row, index) {
                    return row.pvms ? row?.pvms?.nomenclature : "";
                },
            },
            {
                data: "null",
                name: "unit_name",
                render: function (data, type, row) {
                    return row.pvms && row.pvms.unit_name
                        ? row.pvms.unit_name.name
                        : "";
                },
            },
            {
                data: "null",
                name: "unit",
                render: function (data, type, row) {
                    return row.unit ? row.unit.name : "";
                },
            },
            {
                data: "null",
                name: "batch",
                render: function (data, type, row) {
                    return row.batch ? row.batch.batch_no : "";
                },
            },
            {
                data: "null",
                name: "qty",
                render: function (data, type, row, index) {
                    return row.stock_in ? row.stock_in : 0;
                },
            },
        ],
        rowCallback: function (row, data, index) {
            $("td:eq(0)", row).html(index + 1); // Incremental numbering starting from 1
        },
    });

    $(
        "#consilidated-annual-demand #year_id, #consilidated-annual-demand #unit_id"
    ).on("change", function () {
        consilidatedAnnualDemand.draw();
    });

    $("#consilidated-annual-demand #pvms_no").on("keyup", function () {
        consilidatedAnnualDemand.draw();
    });

    var stockTransitPvmsListTable = $("#stockTransitPvmsListTable").DataTable({
        // "bFilter": false,
        // "bLengthChange": false,
        processing: true,
        serverSide: true,
        pageLength: 25,
        ordering: false,
        pagingType: "simple_numbers",
        ajax: {
            url: "/report/pvms-transit-list-api",
            data: function (d) {
                d.length = $("#stockTransitPvmsListTable")
                    .DataTable()
                    .page.len();
                d.start = $("#stockTransitPvmsListTable")
                    .DataTable()
                    .page.info().start;
                d.search = $('input[type="search"]').val();
                d.sub_org_id = $("#sub_org_id").val();
            },
        },
        // language: {
        //     paginate: {
        //         previous: '<button id="prev-btn" class="btn btn-sm btn-primary">Previous</button>',
        //         next: '<button id="next-btn" class="btn btn-sm btn-primary px-4">Next</button>'
        //     }
        // },
        columns: [
            {
                data: "null",
                name: "Sl",
                render: function (data, type, row, index) {
                    return "";
                },
            },
            {
                data: "null",
                name: "Pvms Id",
                render: function (data, type, row, index) {
                    return row.pvms ? row?.pvms?.pvms_id : "";
                },
            },
            {
                data: "null",
                name: "Pvms Id",
                render: function (data, type, row, index) {
                    return row.pvms ? row?.pvms?.nomenclature : "";
                },
            },
            {
                data: "null",
                name: "unit_name",
                render: function (data, type, row) {
                    return row.pvms && row.pvms.unit_name
                        ? row.pvms.unit_name.name
                        : "";
                },
            },
            {
                data: "null",
                name: "unit",
                render: function (data, type, row) {
                    return row.unit ? row.unit.name : "";
                },
            },
            {
                data: "null",
                name: "batch",
                render: function (data, type, row) {
                    return row.batch ? row.batch.batch_no : "";
                },
            },
            {
                data: "null",
                name: "qty",
                render: function (data, type, row, index) {
                    return row.stock_in ? row.stock_in : 0;
                },
            },
        ],
        rowCallback: function (row, data, index) {
            $("td:eq(0)", row).html(index + 1); // Incremental numbering starting from 1
        },
    });

    $("#sub_org_id").on("change", function () {
        stockTransitPvmsListTable.draw();
    });

    var stockOnLoanPvmsListTable = $("#stockOnLoanPvmsListTable").DataTable({
        // "bFilter": false,
        // "bLengthChange": false,
        processing: true,
        serverSide: true,
        pageLength: 25,
        ordering: false,
        pagingType: "simple_numbers",
        ajax: {
            url: "/report/pvms-on-loan-list-api",
            data: function (d) {
                d.length = $("#stockOnLoanPvmsListTable")
                    .DataTable()
                    .page.len();
                d.start = $("#stockOnLoanPvmsListTable")
                    .DataTable()
                    .page.info().start;
                d.search = $('input[type="search"]').val();
            },
        },
        // language: {
        //     paginate: {
        //         previous: '<button id="prev-btn" class="btn btn-sm btn-primary">Previous</button>',
        //         next: '<button id="next-btn" class="btn btn-sm btn-primary px-4">Next</button>'
        //     }
        // },
        columns: [
            {
                data: "null",
                name: "Sl",
                render: function (data, type, row, index) {
                    return "";
                },
            },
            {
                data: "null",
                name: "Ref No",
                render: function (data, type, row, index) {
                    return row.on_loan ? row?.on_loan?.reference_no : "";
                },
            },
            {
                data: "null",
                name: "Vendor",
                render: function (data, type, row, index) {
                    return row.on_loan && row.on_loan.vendor
                        ? row.on_loan.vendor?.name
                        : "";
                },
            },
            {
                data: "null",
                name: "Pvms Id",
                render: function (data, type, row) {
                    return row.p_v_m_s && row.p_v_m_s.pvms_id
                        ? row.p_v_m_s.pvms_id
                        : "";
                },
            },
            {
                data: "null",
                name: "nomenclature",
                render: function (data, type, row) {
                    return row.p_v_m_s && row.p_v_m_s.nomenclature
                        ? row.p_v_m_s.nomenclature
                        : "";
                },
            },
            {
                data: "null",
                name: "au",
                render: function (data, type, row) {
                    return row.p_v_m_s && row.p_v_m_s.unit_name
                        ? row.p_v_m_s.unit_name.name
                        : "";
                },
            },
            {
                data: "null",
                name: "qty",
                render: function (data, type, row, index) {
                    return row.qty ? row.qty : 0;
                },
            },
            {
                data: "null",
                name: "receieved_qty",
                render: function (data, type, row, index) {
                    return row.receieved_qty ? row.receieved_qty : 0;
                },
            },
            {
                data: "null",
                name: "adjusted",
                render: function (data, type, row, index) {
                    return row.pvms_store
                        ? row.pvms_store.reduce((prev, curr) => {
                              if (curr.is_on_loan == 0) {
                                  return prev + curr.stock_in;
                              } else {
                                  return prev;
                              }
                          }, 0)
                        : 0;
                },
            },
            {
                data: "null",
                name: "left",
                render: function (data, type, row, index) {
                    return row.receieved_qty && row.qty
                        ? row.qty - row.receieved_qty
                        : 0;
                },
            },
        ],
        rowCallback: function (row, data, index) {
            $("td:eq(0)", row).html(index + 1); // Incremental numbering starting from 1
        },
    });
    var stockOnLoanAdjustPvmsListTable = $(
        "#stockOnLoanAdjustPvmsListTable"
    ).DataTable({
        // "bFilter": false,
        // "bLengthChange": false,
        processing: true,
        serverSide: true,
        pageLength: 25,
        ordering: false,
        pagingType: "simple_numbers",
        ajax: {
            url: "/report/pvms-on-loan-adjust-list-api",
            data: function (d) {
                d.length = $("#stockOnLoanAdjustPvmsListTable")
                    .DataTable()
                    .page.len();
                d.start = $("#stockOnLoanAdjustPvmsListTable")
                    .DataTable()
                    .page.info().start;
                d.search = $('input[type="search"]').val();
                d.date_range = $('input[name="datefilter"]').val();
            },
        },
        // language: {
        //     paginate: {
        //         previous: '<button id="prev-btn" class="btn btn-sm btn-primary">Previous</button>',
        //         next: '<button id="next-btn" class="btn btn-sm btn-primary px-4">Next</button>'
        //     }
        // },
        columns: [
            {
                data: "null",
                name: "Sl",
                render: function (data, type, row, index) {
                    return "";
                },
            },
            {
                data: "null",
                name: "Ref No",
                render: function (data, type, row, index) {
                    return row.workorder_pvms && row.workorder_pvms.workorder
                        ? row.workorder_pvms.workorder?.contract_number
                        : "";
                },
            },
            {
                data: "null",
                name: "Pvms Id",
                render: function (data, type, row) {
                    return row.workorder_pvms && row.workorder_pvms.pvms
                        ? row.workorder_pvms.pvms?.pvms_id
                        : "";
                },
            },
            {
                data: "null",
                name: "nomenclature",
                render: function (data, type, row) {
                    return row.workorder_pvms && row.workorder_pvms.pvms
                        ? row.workorder_pvms.pvms?.nomenclature
                        : "";
                },
            },
            {
                data: "null",
                name: "au",
                render: function (data, type, row) {
                    return row.workorder_pvms &&
                        row.workorder_pvms.pvms &&
                        row.workorder_pvms.pvms.unit_name
                        ? row.workorder_pvms.pvms.unit_name?.name
                        : "";
                },
            },
            {
                data: "received_qty",
            },
            {
                data: "null",
                name: "received_on",
                render: function (data, type, row, index) {
                    return moment(row.created_at).format("lll");
                },
            },
        ],
        rowCallback: function (row, data, index) {
            $("td:eq(0)", row).html(index + 1); // Incremental numbering starting from 1
        },
    });

    $('input[name="datefilter"]').on(
        "apply.daterangepicker",
        function (ev, picker) {
            $(this).val(
                picker.startDate.format("DD/MM/YYYY") +
                    " - " +
                    picker.endDate.format("DD/MM/YYYY")
            );
            stockOnLoanAdjustPvmsListTable.draw();
        }
    );

    $('input[name="datefilter"]').on(
        "cancel.daterangepicker",
        function (ev, picker) {
            $(this).val("");
            stockOnLoanAdjustPvmsListTable.draw();
        }
    );

    var voucherDispatchList = $("#voucherDispatchList").DataTable({
        bFilter: true,
        // "bLengthChange": false,
        processing: true,
        serverSide: true,
        pageLength: 25,
        ordering: false,
        pagingType: "simple_numbers",
        ajax: {
            url: "/report/voucher-dispatch-list-api",
            data: function (d) {
                d.length = $("#voucherDispatchList").DataTable().page.len();
                d.start = $("#voucherDispatchList")
                    .DataTable()
                    .page.info().start;
                d.search = $('input[type="search"]').val();
                d.date_range = $('input[name="datefilter"]').val();
            },
        },
        // language: {
        //     paginate: {
        //         previous: '<button id="prev-btn" class="btn btn-sm btn-primary">Previous</button>',
        //         next: '<button id="next-btn" class="btn btn-sm btn-primary px-4">Next</button>'
        //     }
        // },
        columns: [
            {
                data: "null",
                name: "Sl",
                render: function (data, type, row, index) {
                    return "";
                },
            },
            {
                data: "null",
                name: "date",
                render: function (data, type, row, index) {
                    return moment(row.created_at).format("DD/MM/YYYY");
                },
            },
            {
                data: "purchase_number",
            },
            {
                data: "null",
                name: "unit",
                render: function (data, type, row) {
                    return row.dmd_unit ? row.dmd_unit?.name : "";
                },
            },
            {
                data: "purchase_pvms_count",
            },
        ],
        rowCallback: function (row, data, index) {
            $("td:eq(0)", row).html(index + 1); // Incremental numbering starting from 1
        },
    });

    $('input[name="datefilter"]').on(
        "apply.daterangepicker",
        function (ev, picker) {
            $(this).val(
                picker.startDate.format("DD/MM/YYYY") +
                    " - " +
                    picker.endDate.format("DD/MM/YYYY")
            );
            voucherDispatchList.draw();
        }
    );

    $('input[name="datefilter"]').on(
        "cancel.daterangepicker",
        function (ev, picker) {
            $(this).val("");
            voucherDispatchList.draw();
        }
    );

    var supplySourceList = $("#supplySourceList").DataTable({
        // "bFilter": false,
        // "bLengthChange": false,
        processing: true,
        serverSide: true,
        pageLength: 25,
        ordering: false,
        pagingType: "simple_numbers",
        ajax: {
            url: "/report/supply-source-list-api",
            data: function (d) {
                d.length = $("#supplySourceList").DataTable().page.len();
                d.start = $("#supplySourceList").DataTable().page.info().start;
                d.search = $('input[type="search"]').val();
                d.fy = $("#fy_id").val();
            },
        },
        // language: {
        //     paginate: {
        //         previous: '<button id="prev-btn" class="btn btn-sm btn-primary">Previous</button>',
        //         next: '<button id="next-btn" class="btn btn-sm btn-primary px-4">Next</button>'
        //     }
        // },
        columns: [
            {
                data: "null",
                name: "Sl",
                render: function (data, type, row, index) {
                    return "";
                },
            },
            { data: "pvms_name" },
            {
                data: "nomenclature",
            },
            {
                data: "au",
            },
            {
                data: "spec",
            },
            {
                data: "user_name",
            },
            {
                data: "total_qty",
            },
            {
                data: "total_received_qty",
            },
            {
                data: "null",
                name: "rest",
                render: function (data, type, row, index) {
                    return (
                        parseInt(row.total_qty) -
                        parseInt(row.total_received_qty)
                    );
                },
            },
        ],
        rowCallback: function (row, data, index) {
            $("td:eq(0)", row).html(index + 1); // Incremental numbering starting from 1
        },
        bDestroy: true,
    });

    $("#fy_id").on("change", function () {
        var selectedYear = $(this).val();
        if (selectedYear) {
            supplySourceList.ajax.reload();
        } else {
            supplySourceList.clear().draw();
        }
    });

    var stockPositionPvmsListTable = $("#stockPositionPvmsListTable").DataTable(
        {
            // "bFilter": false,
            // "bLengthChange": false,
            processing: true,
            serverSide: true,
            pageLength: 25,
            ordering: false,
            pagingType: "simple_numbers",
            ajax: {
                url: "/report/pvms-stock-position-list-api",
                data: function (d) {
                    d.length = $("#stockPositionPvmsListTable")
                        .DataTable()
                        .page.len();
                    d.start = $("#stockPositionPvmsListTable")
                        .DataTable()
                        .page.info().start;
                    d.search = $('input[type="search"]').val();
                    d.sub_org_id = $("#sub_org_id").val();
                    d.pvms_item_type = $("#pvms_item_type").val();
                    // d.date_range = $('input[name="datefilter"]').val();
                },
            },
            // language: {
            //     paginate: {
            //         previous: '<button id="prev-btn" class="btn btn-sm btn-primary">Previous</button>',
            //         next: '<button id="next-btn" class="btn btn-sm btn-primary px-4">Next</button>'
            //     }
            // },
            columns: [
                {
                    data: "null",
                    name: "Sl",
                    render: function (data, type, row, index) {
                        return "";
                    },
                },
                { data: "pvms_id" },
                { data: "nomenclature" },
                {
                    data: "null",
                    name: "unit_name",
                    render: function (data, type, row) {
                        return row.unit_name ? row.unit_name.name : "";
                    },
                },
                {
                    data: "null",
                    name: "item_group_name",
                    render: function (data, type, row) {
                        return row.item_group_name
                            ? row.item_group_name.name
                            : "";
                    },
                },

                {
                    data: "null",
                    name: "balance",
                    render: function (data, type, row) {
                        return row.stock_qty ? row.stock_qty : 0;
                    },
                },
                {
                    data: "null",
                    name: "last_receieved_date",
                    render: function (data, type, row) {
                        return row.latest_stock_date
                            ? moment(row.latest_stock_date).format("DD/MM/YYYY")
                            : "";
                    },
                },
                {
                    data: "null",
                    name: "latest_stock_qty",
                    render: function (data, type, row, index) {
                        return row.latest_stock_qty ? row.latest_stock_qty : 0;
                    },
                },
                {
                    data: "null",
                    name: "upcoming_expire_date",
                    render: function (data, type, row) {
                        return row.upcoming_expire_date
                            ? moment(row.upcoming_expire_date).format(
                                  "DD/MM/YYYY"
                              )
                            : "";
                    },
                },
            ],
            rowCallback: function (row, data, index) {
                $("td:eq(0)", row).html(index + 1); // Incremental numbering starting from 1
            },
        }
    );

    $("#sub_org_id").on("change", function () {
        stockPositionPvmsListTable.draw();
    });
    $("#pvms_item_type").on("change", function () {
        stockPositionPvmsListTable.draw();
    });

    toastr.options = {
        closeButton: true,
        progressBar: true,
    };

    if ($("#session-success").length) {
        toastr.success($("#session-success").html());
    }
    if ($("#session-error").length) {
        toastr.error($("#session-error").html());
    }
    if ($("#session-info").length) {
        toastr.info($("#session-info").html());
    }
    if ($("#session-warning").length) {
        toastr.warning($("#session-warning").html());
    }
    var stockExpireDateWisesPvmsListTable = $(
        "#stockExpireDateWisesPvmsListTable"
    ).DataTable({
        // "bFilter": false,
        // "bLengthChange": false,
        processing: true,
        serverSide: true,
        pageLength: 25,
        ordering: false,
        pagingType: "simple_numbers",
        ajax: {
            url: "/report/pvms-expire-date-wise-list-api",
            data: function (d) {
                d.length = $("#stockExpireDateWisesPvmsListTable")
                    .DataTable()
                    .page.len();
                d.start = $("#stockExpireDateWisesPvmsListTable")
                    .DataTable()
                    .page.info().start;
                d.search = $('input[type="search"]').val();
                d.sub_org_id = $("#sub_org_id").val();
                d.pvms_item_type = $("#pvms_item_type").val();
                d.no_of_months = $("#month_durations").val();
            },
        },
        // language: {
        //     paginate: {
        //         previous: '<button id="prev-btn" class="btn btn-sm btn-primary">Previous</button>',
        //         next: '<button id="next-btn" class="btn btn-sm btn-primary px-4">Next</button>'
        //     }
        // },
        columns: [
            {
                data: "null",
                name: "Sl",
                render: function (data, type, row, index) {
                    return "";
                },
            },
            {
                data: "null",
                name: "fy",
                render: function (data, type, row, index) {
                    return row.fy ? row.fy : "";
                },
            },
            { data: "pvms_uniq_id" },
            { data: "nomenclature" },
            {
                data: "null",
                name: "au",
                render: function (data, type, row) {
                    return row.au ? row.au : "";
                },
            },
            {
                data: "null",
                name: "item_group_name",
                render: function (data, type, row) {
                    return row.ig ? row.ig : "";
                },
            },

            {
                data: "null",
                name: "vendor_name",
                render: function (data, type, row) {
                    return row.vendor_name ? row.vendor_name : "";
                },
            },
            {
                data: "null",
                name: "contract_no",
                render: function (data, type, row) {
                    return row.contract_no ? row.contract_no : "";
                },
            },
            {
                data: "null",
                name: "crv_no",
                render: function (data, type, row) {
                    return row.crv_no ? row.crv_no : "";
                },
            },
            { data: "stock_in" },
            {
                data: "null",
                name: "order_no",
                render: function (data, type, row) {
                    return row.created_at
                        ? moment(row.created_at).format("DD/MM/YYYY")
                        : "";
                },
            },
            {
                data: "null",
                name: "expire_date",
                render: function (data, type, row) {
                    return row.expire_date
                        ? moment(row.expire_date).format("DD/MM/YYYY")
                        : "";
                },
            },
        ],
        rowCallback: function (row, data, index) {
            $("td:eq(0)", row).html(index + 1); // Incremental numbering starting from 1
        },
    });

    $("#sub_org_id").on("change", function () {
        stockExpireDateWisesPvmsListTable.draw();
    });
    $("#pvms_item_type").on("change", function () {
        stockExpireDateWisesPvmsListTable.draw();
    });
    $("#month_durations").on("change", function () {
        stockExpireDateWisesPvmsListTable.draw();
    });

    $("#pvms-table .search").keyup(function () {
        const keyword = $(this).val();

        if (keyword == "") {
            window.location.reload();
        }

        $(".pagination").addClass("dis-none");

        $.get(
            window.app_url + "/settings/pvms/search?keyword=" + keyword,
            function (res) {
                let items = [];
                let index = 1;
                for (const iterator of res) {
                    items.push(`
                <tr>
                    <td>${index++}</td>
                    <td>${iterator.pvms_id}</td>
                    <td>${iterator.nomenclature}</td>
                    <td>${iterator.unit_name?.name}</td>
                    <td>${
                        iterator.specification_name?.name
                            ? iterator.specification_name?.name
                            : ""
                    }</td>
                    <td>${
                        iterator.item_group_name?.name
                            ? iterator.item_group_name?.name
                            : ""
                    }</td>
                    <td>${
                        iterator.item_sections_id
                            ? iterator.item_sections_id
                            : ""
                    }</td>
                    <td>${iterator.item_typename?.name}</td>
                    <td class="d-flex">
                        <a href="/settings/edit/pvms/${iterator.id}">
                            <button class="btn btn-outline-info border-0">
                                <i class="fas fa-edit"></i>
                            </button>
                        </a>
                        ${
                            window.canAddPvmsStock
                                ? `<a href="/settings/add-pvms-stock/pvms/${iterator.id}">
                                    <button class="btn btn-outline-info border-0">
                                        <i class="fas fa-plus-circle"></i>
                                    </button>
                                </a>`
                                : ""
                        }
                        <form action="/settings/delete/pvms/${
                            iterator.id
                        }" method="post">
                            <input type="hidden" name="_token" value="${$(
                                "meta[name=csrf-token]"
                            ).attr(
                                "content"
                            )}" autocomplete="off">                                                            <button type="submit" id="60777" class="border-0 btn-transition btn btn-outline-danger delete-account-unit"><i class="fa fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>
                `);
                }
                $("#pvms-tbody").html(items.join(""));
            }
        );
    });

    $("#pvms-table .limit").change(function () {
        console.log($(this).parent().submit());
    });
    $("#demand-table .perpage").change(function () {
        $("#demand-table").submit();
    });
    $("#demand-table .type").change(function () {
        $("#demand-table").submit();
    });

    var stockPvmsListTransitionTable = $(
        "#stockPvmsListTransitionTable"
    ).DataTable({
        // "bFilter": false,
        // "bLengthChange": false,
        processing: true,
        serverSide: true,
        pageLength: 25,
        ordering: false,
        pagingType: "simple_numbers",
        ajax: {
            url: "/report/pvms-stock-transition-api",
            data: function (d) {
                d.length = $("#stockPvmsListTransitionTable")
                    .DataTable()
                    .page.len();
                d.start = $("#stockPvmsListTransitionTable")
                    .DataTable()
                    .page.info().start;
                d.search = $('input[type="search"]').val();
                d.sub_org_id = $("#sub_org_id").val();
                d.pvms_item_type = $("#pvms_item_type").val();
                d.date_range = $('input[name="datefilter"]').val();
            },
        },
        // language: {
        //     paginate: {
        //         previous: '<button id="prev-btn" class="btn btn-sm btn-primary">Previous</button>',
        //         next: '<button id="next-btn" class="btn btn-sm btn-primary px-4">Next</button>'
        //     }
        // },
        columns: [
            {
                data: "null",
                name: "Sl",
                render: function (data, type, row, index) {
                    return "";
                },
            },
            {
                data: "null",
                name: "pvms_id",
                render: function (data, type, row) {
                    return row.pvms ? row.pvms.pvms_id : "";
                },
            },
            {
                data: "null",
                name: "nomenclature",
                render: function (data, type, row) {
                    return row.pvms ? row.pvms.nomenclature : "";
                },
            },
            {
                data: "null",
                name: "unit_name",
                render: function (data, type, row) {
                    return row.pvms && row.pvms.unit_name
                        ? row.pvms.unit_name.name
                        : "";
                },
            },
            {
                data: "null",
                name: "specification_name",
                render: function (data, type, row) {
                    return row.pvms && row.pvms.specification_name
                        ? row.pvms.specification_name.name
                        : "";
                },
            },
            {
                data: "null",
                name: "item_group_name",
                render: function (data, type, row) {
                    return row.pvms && row.pvms.item_group_name
                        ? row.pvms.item_group_name.name
                        : "";
                },
            },

            {
                data: "null",
                name: "itemSectionName",
                render: function (data, type, row) {
                    return row.pvms && row.pvms.item_section_name
                        ? row.pvms.item_section_name.name
                        : "";
                },
            },
            {
                data: "null",
                name: "item_typename",
                render: function (data, type, row) {
                    return row.pvms && row.pvms.item_typename
                        ? row.pvms.item_typename.name
                        : "";
                },
            },
            {
                data: "null",
                name: "unit",
                render: function (data, type, row) {
                    return row.unit ? row.unit.name : "";
                },
            },
            {
                data: "null",
                name: "ward",
                render: function (data, type, row) {
                    return row.ward ? row.ward.name : "";
                },
            },
            {
                data: "null",
                name: "batch",
                render: function (data, type, row, index) {
                    return row.batch ? row.batch.batch_no : "";
                },
            },
            {
                data: "null",
                name: "expire_date",
                render: function (data, type, row, index) {
                    return row.batch
                        ? moment(row.batch.expire_date).format("DD/MM/YYYY")
                        : "";
                },
            },
            {
                data: "null",
                name: "transition_qty",
                render: function (data, type, row, index) {
                    return row.stock_out ? row.stock_out : 0;
                },
            },
            {
                data: "null",
                name: "date",
                render: function (data, type, row, index) {
                    return row.created_at
                        ? moment(row.created_at).format("DD/MM/YYYY")
                        : "";
                },
            },
        ],
        rowCallback: function (row, data, index) {
            $("td:eq(0)", row).html(index + 1); // Incremental numbering starting from 1
        },
    });

    $("#sub_org_id").on("change", function () {
        stockPvmsListTransitionTable.draw();
    });
    $("#pvms_item_type").on("change", function () {
        stockPvmsListTransitionTable.draw();
    });

    $('input[name="datefilter"]').on(
        "apply.daterangepicker",
        function (ev, picker) {
            $(this).val(
                picker.startDate.format("DD/MM/YYYY") +
                    " - " +
                    picker.endDate.format("DD/MM/YYYY")
            );
            stockPvmsListTransitionTable.draw();
        }
    );

    $('input[name="datefilter"]').on(
        "cancel.daterangepicker",
        function (ev, picker) {
            $(this).val("");
            stockPvmsListTransitionTable.draw();
        }
    );

    $(".main-side-menu").click(function () {
        const is_open = $(this).find("ul").hasClass("open");
        console.log(is_open);
        $(".main-side-menu").find("ul").removeClass("open");
        const this_ul = $(this).find("ul");
        if (!is_open) {
            this_ul.addClass("open");
        }
    });

    $(".close-sidebar-btn").click(function () {
        $(".app-container").toggleClass("closed-sidebar");
    });

    $('a[data-toggle="modal"]').click(function () {
        $($(this).data("target")).addClass("show");
        $("body").append('<div class="modal-backdrop fade show"></div>');
    });

    $(".logout-btn").click(function () {
        $(this).find(".btn-group").toggleClass("show");
        $(this).find(".dropdown-menu").toggleClass("show");
    });

    $(".modal").click(function (e) {
        if (e.target.classList.contains("modal") || e.target.dataset.dismiss) {
            $(this).removeClass("show");
            $(".modal-backdrop").remove();
        }
    });

    var companyOrderDueList = $("#companyOrderDueList").DataTable({
        // "bFilter": false,
        // "bLengthChange": false,
        processing: true,
        serverSide: true,
        pageLength: 25,
        ordering: false,
        pagingType: "simple_numbers",
        ajax: {
            url: "/report/company-due-order-api",
            data: function (d) {
                d.length = $("#companyOrderDueList").DataTable().page.len();
                d.start = $("#companyOrderDueList")
                    .DataTable()
                    .page.info().start;
                d.search = $('input[type="search"]').val();
                d.fy = $("#fy").val();
                d.vendor = $('input[name="vendor"]').val();
            },
        },
        // language: {
        //     paginate: {
        //         previous: '<button id="prev-btn" class="btn btn-sm btn-primary">Previous</button>',
        //         next: '<button id="next-btn" class="btn btn-sm btn-primary px-4">Next</button>'
        //     }
        // },
        columns: [
            {
                data: "null",
                name: "Sl",
                render: function (data, type, row, index) {
                    return "";
                },
            },
            {
                data: "null",
                name: "pvms_id",
                render: function (data, type, row) {
                    return row.pvms ? row.pvms.pvms_id : "";
                },
            },
            {
                data: "null",
                name: "nomenclature",
                render: function (data, type, row) {
                    return row.pvms ? row.pvms.nomenclature : "";
                },
            },
            {
                data: "null",
                name: "unit_name",
                render: function (data, type, row) {
                    return row.pvms && row.pvms.unit_name
                        ? row.pvms.unit_name.name
                        : "";
                },
            },
            {
                data: "qty",
            },
            {
                data: "null",
                name: "workorder_receive_pvms",
                render: function (data, type, row) {
                    return row.workorder_receive_pvms &&
                        row.workorder_receive_pvms[0]
                        ? row.workorder_receive_pvms[0].total_received_qty
                        : 0;
                },
            },

            {
                data: "null",
                name: "due",
                render: function (data, type, row) {
                    return row.workorder_receive_pvms &&
                        row.workorder_receive_pvms[0]
                        ? row.qty -
                              row.workorder_receive_pvms[0].total_received_qty
                        : row.qty;
                },
            },
            {
                data: "unit_price",
            },
            {
                data: "null",
                name: "contract_date",
                render: function (data, type, row) {
                    return row.workorder && row.workorder.contract_date
                        ? moment(row.workorder.contract_date).format(
                              "DD/MM/YYYY"
                          )
                        : "";
                },
            },
            {
                data: "null",
                name: "contract_no",
                render: function (data, type, row) {
                    return row.workorder ? row.workorder.contract_number : "";
                },
            },
            {
                data: "null",
                name: "vendor",
                render: function (data, type, row, index) {
                    return row.workorder && row.workorder.vendor
                        ? row.workorder.vendor.name
                        : "";
                },
            },
            {
                data: "null",
                name: "last_sub_sate",
                render: function (data, type, row, index) {
                    return row.workorder && row.workorder.last_submit_date
                        ? moment(row.workorder.last_submit_date).format(
                              "DD/MM/YYYY"
                          )
                        : "";
                },
            },
            {
                data: "null",
                name: "last_receieved_date",
                render: function (data, type, row, index) {
                    return row.workorder_receive_pvms &&
                        row.workorder_receive_pvms[0] &&
                        row.workorder_receive_pvms[0].last_received_date
                        ? moment(
                              row.workorder_receive_pvms[0].last_received_date
                          ).format("DD/MM/YYYY")
                        : "";
                },
            },
            {
                data: "null",
                name: "compare_day",
                render: function (data, type, row, index) {
                    return row.workorder &&
                        row.workorder.last_submit_date &&
                        row.workorder_receive_pvms &&
                        row.workorder_receive_pvms[0] &&
                        row.workorder_receive_pvms[0].last_received_date
                        ? moment(row.workorder.last_submit_date).diff(
                              moment(
                                  row.workorder_receive_pvms[0]
                                      .last_received_date
                              ),
                              "days"
                          )
                        : "";
                },
            },
            {
                data: "null",
                name: "stock_qty",
                render: function (data, type, row, index) {
                    return row.stock_qty ? row.stock_qty : 0;
                },
            },
        ],
        initComplete: function () {
            // Add placeholder to the search input
            $("#companyOrderDueList_filter input").attr(
                "placeholder",
                "Search Contract No..."
            );
        },
        rowCallback: function (row, data, index) {
            $("td:eq(0)", row).html(index + 1); // Incremental numbering starting from 1
        },
    });

    $("#fy").on("change", function () {
        debugger;
        companyOrderDueList.draw();
    });

    $('input[name="vendor"]').on("change", function () {
        debugger;
        companyOrderDueList.draw();
    });
    $(document).on("vendorSelectChange", function (event, selectedOption) {
        debugger;
        companyOrderDueList.draw();
    });

    document.addEventListener("contextmenu", (event) => event.preventDefault());

    const main_menus = $("#all-menus a");
    const pathname = window.location.href;

    $.each(main_menus, function (key, val) {
        const menu = $(this);
        if (menu.attr("href") == pathname) {
            const parent_element = menu.parent();
            menu.addClass("mm-active");
            menu.parent().parent().addClass("open");
            menu.parent().find(".sidebar-icon i").addClass("active");

            if (parent_element.hasClass("sub-menus")) {
                const outer_parent = parent_element.parent();
                const most_outer_parent = outer_parent.parent();
                outer_parent.addClass("mm-collapse mm-show");
                most_outer_parent.addClass("mm-active");
            }
        }
    });

    $.get(window.app_url + "/workorder/new", function (res) {
        $("#new_workorder_count").html(
            `<div class="pl-2"><span class="badge badge-warning">${res}</span></div>`
        );
    });
});
