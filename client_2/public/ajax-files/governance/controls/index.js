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
        { data: "id" },
        { data: "short_name" },
        { data: "description" },
        { data: "Frameworks", orderable: false },
        { data: "family_with_parent", orderable: false },
        { data: "family_name", orderable: false },
        { data: "control_status" },
        { data: "Actions" },
    ],
    // columnDefinitions
    [
        {
            targets: 2, // Index for 'description'
            render: function (data, type, full, meta) {
                console.log(data);

                // Handle null or undefined data
                const safeData = data || ""; // Default to an empty string if data is null or undefined

                const previewText =
                    safeData.length > 100
                        ? safeData.substring(0, 50) +
                          `<span style="color: #2196F3; cursor: pointer;">... See more</span>`
                        : safeData;

                return `
                    <span class="description-preview" data-description="${safeData}">
                        ${previewText}
                    </span>
                `;
            },
        },
        { width: "30%", targets: 3 },
        {
            // Actions
            targets: -1,
            orderable: false,
            render: function (data, type, full, meta) {
                let returnedString = "";
                let auditCreateString = "";
                let objectiveString = "";
                let editString = "";
                let deleteString = "";

                // if (permission["audits.create"] && !full.isParent) {
                //     auditCreateString +=
                //         '<a  href="javascript:;" onclick="showModalCreateAudit(' +
                //         data +
                //         ')" class="item-edit dropdown-item ">' +
                //         feather.icons["edit"].toSvg({
                //             class: "me-50 font-small-4",
                //         }) +
                //         `${lang['Audit']}</a>`;
                // }

                if (permission["list_objectives"] && !full.isParent) {
                    objectiveString +=
                        '<a  href="javascript:;" onclick="showControlObjectives(' +
                        data +
                        ')" class="item-edit dropdown-item btn-flat-warning">' +
                        feather.icons["list"].toSvg({
                            class: "me-50 font-small-4",
                        }) +
                        `${lang["Objective"]}</a>`;
                }
                if (permission["edit"]) {
                    editString +=
                        '<a  href="javascript:;" onclick="editControl(' +
                        data +
                        ')" class="item-edit dropdown-item ">' +
                        feather.icons["edit"].toSvg({
                            class: "me-50 font-small-4",
                        }) +
                        `${lang["Edit"]}</a>`;
                }

                if (permission["delete"]) {
                    deleteString +=
                        '<a  href="javascript:;" onclick="deleteControl(' +
                        data +
                        ')" class="dropdown-item  btn-flat-danger">' +
                        feather.icons["trash-2"].toSvg({
                            class: "me-50 font-small-4",
                        }) +
                        `${lang["Delete"]}</a>`;
                }

                return (
                    '<div class="d-inline-flex">' +
                    '<a class="pe-1 dropdown-toggle hide-arrow text-primary" data-bs-toggle="dropdown">' +
                    feather.icons["more-vertical"].toSvg({
                        class: "font-small-4",
                    }) +
                    "</a>" +
                    '<div class="dropdown-menu dropdown-menu-end">' +
                    auditCreateString +
                    editString +
                    objectiveString +
                    '<a  href="javascript:;" onclick="mapControl(' +
                    data +
                    ')" class="dropdown-item  btn-flat-success">' +
                    feather.icons["git-merge"].toSvg({
                        class: "font-small-4",
                    }) +
                    `${lang["Mapping"]}</a>` +
                    deleteString +
                    "</div>" +
                    "</div>"
                );
            },
        },
    ],
    // detailsOfItem
    lang["DetailsOfItem"],
    // detailsOfItemKey
    "name"
);
function toggleDescription(id) {
    const fullDescription = document.getElementById(`description-full-${id}`);
    const seeMoreLink = document.getElementById(`see-more-${id}`);
    const seeLessLink = document.getElementById(`see-less-${id}`);

    if (fullDescription.style.display === "none") {
        fullDescription.style.display = "block"; // Show full description
        seeMoreLink.style.display = "none"; // Hide "See More"
        seeLessLink.style.display = "inline"; // Show "See Less"
    } else {
        fullDescription.style.display = "none"; // Hide full description
        seeMoreLink.style.display = "inline"; // Show "See More"
        seeLessLink.style.display = "none"; // Hide "See Less"
    }
}
