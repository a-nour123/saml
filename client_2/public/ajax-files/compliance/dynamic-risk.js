//  ajax to call risk list and call create datatable
function loadDatatable() {
    $.ajax({
        url: listURL,
        type: "GET",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {},
        success: function (data) {
            createDatatable(data);
        },
        error: function () {
            //
        },
    });
}
loadDatatable(); // Load table for first time

// Reset form
function resetFormData(form) {
    $(".error").empty();
    form.trigger("reset");
    form.find('input:not([name="_token"])').val("");
    form.find("select.multiple-select2 option[selected]").attr(
        "selected",
        false
    );
    form.find("select.select2 option").attr("selected", false);
    form.find("select.select2").each(function (index) {
        $(this).find("option").first().attr("selected", true);
    });
    form.find("select").trigger("change");
}

$(".modal").on("hidden.bs.modal", function () {
    resetFormData($(this).find("form"));
});

$(document).ready(function () {
    $(".multiple-select2").select2();

    // Load controls of framework
    $("[name='framework_id']").on("change", function () {
        const frameworkControls = $(this)
            .find("option:selected")
            .data("controls");
        $("[name='control_id']").find("option:not(:first)").remove();
        $("[name='control_id']").find("option:first").attr("selected", true);
        if (frameworkControls)
            frameworkControls.forEach((frameworkControl) => {
                $("[name='control_id']").append(
                    `<option value="${frameworkControl.id}">${frameworkControl.short_name}</option>`
                );
            });
    });

    // Load Owner manager
    $("[name='owner_id']").on("change", function () {
        const ownerManger = $(this).find("option:selected").data("manager");
        $("[name='owner_manager_id']").find("option:not(:first)").remove();
        $("[name='owner_manager_id']")
            .find("option:first")
            .attr("selected", true);
        if (ownerManger)
            $("[name='owner_manager_id']").append(
                `<option value="${ownerManger.id}">${ownerManger.name}</option>`
            );
    });

    // Submit form for creating risk
    $("#add-new-risk form").submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: createURL,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                if (data.status) {
                    makeAlert(
                        data.alert ? "warning" : "success",
                        data.alert
                            ? `${data.alert}<br>${data.message}`
                            : `${data.message}`,
                        lang["success"]
                    );
                    $("#add-new-risk").modal("hide");
                    $("#advanced-search-datatable").load(
                        location.href + " #advanced-search-datatable>*",
                        ""
                    );
                    loadDatatable();
                } else {
                    showError(data["errors"]);
                }
            },
            error: function (response, data) {
                responseData = response.responseJSON;
                makeAlert("error", responseData.message, lang["error"]);
                showError(responseData.errors);
            },
        });
    });
});

//filter Column
function filterColumn(i, val) {
    $(".dt-advanced-search")
        .DataTable()
        .column(i)
        .search(val, false, true)
        .draw();
}

function createDatatable(JsonList) {
    var isRtl = $("html").attr("data-textdirection") === "rtl";
    var dt_ajax_table = $(".datatables-ajax"),
        dt_filter_table = $(".dt-column-search"),
        dt_adv_filter_table = $(".dt-advanced-search"),
        dt_responsive_table = $(".dt-responsive"),
        assetPath = "../../../app-assets/";
    if ($("body").attr("data-framework") === "laravel") {
        assetPath = $("body").attr("data-asset-path");
    }
    var dt_adv_filter = dt_adv_filter_table.DataTable({
        dom: "Bfrtip",
        data: JsonList,
        responsive: true,
        autoWidth: true,
        searching: true,
        columns: [
            { data: "responsive_id" },
            // { data: "id" },
            { data: "status" },
            { data: "subject" },
            { data: "inherent_risk_current" },
            { data: "created_at" },
            // { data: 'mitigation_planned' },
            // { data: 'management_review' },
            { data: "closure_date" },
            { data: "risk_catalog_mapping" },
            { data: "threat_catalog_mapping" },
            { data: "submitted_by" },
            { data: "source_id" },
            { data: "category_id" },
            { data: "Actions" },
        ],
        columnDefs: [
            {
                title: "#",
                className: "index",
                orderable: false,
                responsivePriority: 2,
                targets: 0,
            },
            {
                targets: 0,
                className: "noVis",
            },
            {
                title: "#",
                className: "index",
                orderable: false,
                responsivePriority: 2,
                targets: 0,
            },
            {
                // Actions
                targets: -1,
                orderable: false,
                render: function (data, type, full, meta) {
                  let url = showURL.replace(":id", data);
              
                  let returnedString = '<div class="dropdown">' +
                      '<a class="pe-1 dropdown-toggle hide-arrow text-primary" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                      feather.icons['more-vertical'].toSvg({ class: 'font-small-4' }) +
                      '</a>' +
                      '<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">';
              
                
                  returnedString += `<li><a class="dropdown-item item-show" href="${url}">` +
                      feather.icons['eye'].toSvg({ class: 'me-50 font-small-4' }) +
                      ' Show</a></li>';
              
                  returnedString += '</ul></div>';
              
                  return returnedString;
                }
              }
              ,
            {
                targets: 3,
                render: function (data, type, full, meta) {
                    return (
                        '<div class="risk-cell-holder" style="position:relative;">' +
                        data[0] +
                        '<span class="risk-color" style="background-color:' +
                        data[1] +
                        ';position: absolute;width: 20px;height: 20px;right: 10px;top: 50%;transform: translateY(-50%);border-radius: 2px;border: 1px solid;"></span></div>'
                    );
                    // return '<span class="badge rounded-pill badge-light-success">' + data + '</span>';
                },
            },
        ],
        buttons: [
            {
                extend: "colvis",
                columns: ":not(.noVis)",
            },
        ],
        orderCellsTop: true,
        language: {
            paginate: {
                previous: "&nbsp;",
                next: "&nbsp;",
            },
        },
        responsive: {
            details: {
                type: "column",
                renderer: function (api, rowIdx, columns) {
                    var data = $.map(columns, function (col, i) {
                        return col.title !== ""
                            ? '<tr data-dt-row="' +
                                  col.rowIndex +
                                  '" data-dt-column="' +
                                  col.columnIndex +
                                  '">' +
                                  "<td>" +
                                  col.title +
                                  ":" +
                                  "</td> " +
                                  "<td>" +
                                  col.data +
                                  "</td>" +
                                  "</tr>"
                            : "";
                    }).join("");

                    return data
                        ? $('<table class="table"/><tbody />').append(data)
                        : false;
                },
            },
        },
    });

    dt_adv_filter
        .on("order.dt search.dt", function () {
            dt_adv_filter
                .column(0, { search: "applied", order: "applied" })
                .nodes()
                .each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
        })
        .draw();
}
// call function in input filter
$("input.dt-input").on("keyup", function () {
    filterColumn($(this).attr("data-column"), $(this).val());
});
// call function in select filter
$("select.dt-select").on("change", function () {
    filterColumn($(this).attr("data-column"), $(this).val());
});
$(".dataTables_filter .form-control").removeClass("form-control-sm");
$(".dataTables_length .form-select")
    .removeClass("form-select-sm")
    .removeClass("form-control-sm");


    