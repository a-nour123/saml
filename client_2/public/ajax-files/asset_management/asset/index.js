//select2 class
$(document).ready(function () {
    $(".multiple-select2").select2();
});

// function to show error validation
function showError(data) {
    $(".error").empty();
    $.each(data, function (key, value) {
        $(".error-" + key).empty();
        $(".error-" + key).append(value);
    });
}

// status [warning, success, error]
function makeAlert($status, message, title) {
    // On load Toast
    if (title == "Success") title = "👋" + title;
    toastr[$status](message, title, {
        closeButton: true,
        tapToDismiss: false,
    });
}

// dataPickr custom for compliance
dateTimePickr = $(".flatpickr-date-time-compliance");
// Date & TIme
if (dateTimePickr.length) {
    dateTimePickr.flatpickr({
        enableTime: false,
        dateFormat: "Y-m-d",
    });
}

drawDatatable(
    // columnsData
    [
        { data: "id", orderable: false },
        { data: "name", orderable: false },
        { data: "ip", orderable: false },
        { data: "value", orderable: false },
        { data: "assetCategory", orderable: false },
        { data: "location", orderable: false },
        { data: "assetOs", orderable: false },
        { data: "assetEnvironmentCategory", orderable: false },
        { data: "owner_email", orderable: false },
        { data: "asset_owner", orderable: false },
        { data: "model", orderable: false },
        { data: "created", orderable: false },
        { data: "updated_at", orderable: false },
        { data: "verified", orderable: false },
        { data: "regions", orderable: false },
        { data: "Actions", orderable: false },
    ],
    // columnDefinitions
    [
        {
            // Actions
            targets: -1,
            orderable: false,
            render: function (data, type, full, meta) {
                let returnedString =
                    '<div class="dropdown">' +
                    '<a class="pe-1 dropdown-toggle hide-arrow text-primary" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical font-small-4">' +
                    '<circle cx="12" cy="12" r="1"></circle>' +
                    '<circle cx="12" cy="5" r="1"></circle>' +
                    '<circle cx="12" cy="19" r="1"></circle>' +
                    "</svg>" +
                    "</a>" +
                    '<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">';

                if (permission["delete"]) {
                    returnedString +=
                        '<li><a class="dropdown-item item-delete" href="javascript:;" onclick="ShowModalDeleteAsset(' +
                        data +
                        ')">' +
                        feather.icons["trash-2"].toSvg({
                            class: "me-50 font-small-4",
                        }) +
                        " Delete</a></li>";
                }

                if (permission["edit"]) {
                    returnedString +=
                        `<li><a class="dropdown-item item-edit" id="asset-${data}" href="javascript:;" onclick="ShowModalEditAsset(${data})">` +
                        feather.icons["edit"].toSvg({ class: "font-small-4" }) +
                        " Edit</a></li>";
                }
                if (
                    role_id === 1 ||
                    data["asset_owner"] !== user_id
                ) {
                    returnedString +=
                        `<li><a class="dropdown-item item-comment" id="asset-${data}" href="javascript:;" onclick="openAssetCommentsModal(${data})">` +
                        feather.icons["message-square"].toSvg({
                            class: "font-small-4",
                        }) +
                        " Comment</a></li>";
                }

                returnedString += "</ul></div>";

                if (
                    returnedString ===
                    '<div class="dropdown">' +
                        '<a class="pe-1 dropdown-toggle hide-arrow text-primary" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">' +
                        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical font-small-4">' +
                        '<circle cx="12" cy="12" r="1"></circle>' +
                        '<circle cx="12" cy="5" r="1"></circle>' +
                        '<circle cx="12" cy="19" r="1"></circle>' +
                        "</svg>" +
                        '</a><ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton"></ul></div>'
                ) {
                    returnedString = "------";
                }

                return returnedString;
            },
        },
        {
            // Label for verified
            targets: 13, // this targets the verified column (13th column, index 12)
            render: function (data, type, full, meta) {
                return `<span class="badge rounded-pill badge-light-${
                    data ? "success" : "danger"
                }">${
                    data ? verifiedTranslation : UnverifiedAssetsTranslation
                }</span>`;
            },
        },
    ],
    // detailsOfItem
    lang["DetailsOfItem"],
    // detailsOfItemKey
    "name"
);
