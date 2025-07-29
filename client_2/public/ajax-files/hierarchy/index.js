/*=========================================================================================
  File Name: ext-component-tree.js
  Description: Tree
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/

// status [warning, success, error]
function makeAlert($status, message, title) {
  // On load Toast
  if (title == 'Success')
    title = '👋' + title;
  toastr[$status](message, title,
    {
      closeButton: true,
      tapToDismiss: false,
    }
  );
}

$(function () {
  'use strict';

  var ajaxTree = $('#jstree-ajax');

  var assetPath = '../../../app-assets/';
  if ($('body').attr('data-framework') === 'laravel') {
    assetPath = $('body').attr('data-asset-path');
  }

  ajaxTree.on("changed.jstree", function (e, data) {
  });

  // Ajax Example
  if (ajaxTree.length) {
    ajaxTree
      .on("changed.jstree", function (e, data) {
        if (data.node.type != 'employee') {
          const id = data.node.id.match(/(D|E)(\d+)/);
          if (id.length == 3) {
            $.ajax({
              url: getDepartmentURL.replace(':id', id[2]),
              type: "GET",
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              success: function (response) {
                if (response.status) {
                  $('#department-details').fadeIn();
                  $('#department-data-name').text(response.data.name)
                  $('#department-data-code').text(response.data.code)
                  $('#department-data-color').text(response.data.color.value).css('color', response.data.color.value)
                  $('#department-data-manager').text(response.data.manager)
                  $('#department-data-parent').text(response.data.parent)
                  $('#department-data-required-num-employee').text(response.data.required_num_emplyees)
                  $('#department-data-actual-num-employee').text(response.data.actual_num_emplyees)
                  $('#department-data-created-at').text(response.data.created_at)
                  
                  quill.setContents(JSON.parse(response.data.vision));
                  $('#department-data-vision').html(quill.root.innerHTML)
                  
                  quill.setContents(JSON.parse(response.data.message));
                  $('#department-data-message').html(quill.root.innerHTML)

                  quill.setContents(JSON.parse(response.data.mission));
                  $('#department-data-mission').html(quill.root.innerHTML)

                  quill.setContents(JSON.parse(response.data.objectives));
                  $('#department-data-objectives').html(quill.root.innerHTML)

                  quill.setContents(JSON.parse(response.data.responsibilities));
                  $('#department-data-responsibilities').html(quill.root.innerHTML)
                }
              },
              error: function (response, data) {
                responseData = response.responseJSON;
                makeAlert('error', responseData.message, "{{ __('locale.Error') }}");
              }
            });
          }
        }

      })
      .on('move_node.jstree', function (e, data) {
        // parent =  # mean parent not founded locate in `root`
        // main parent type =  # mean parent not founded locate in `root`

        const node = data.node,
          parentNode = data.new_instance.get_node(node.parent);
        let requestData = {
          id: node.id,
          _token: $('meta[name="csrf-token"]').attr('content')
        };

        // # mean parent not founded locate in `root`
        if (parentNode.id == '#') {
          requestData.newParentId = null;
          requestData.oldParentId = data.old_parent;
        } else {
          // node is under another node
          requestData.newParentId = data.parent;
          requestData.oldParentId = data.old_parent;
        }
        if (requestData.oldParentId == '#')
          requestData.oldParentId = null;
        $.ajax({
          url: updateUrl
          , type: "PUT"
          , data: requestData
          , success: function (data) {
            if (data.status) {
              makeAlert('success', data.message, success);
            } else {
              makeAlert('error', '', error);
            }
          }
          , error: function (response, data) {
            if (response.responseJSON) {
              makeAlert('error', response.responseJSON.message, error);
            }
            else
              makeAlert('error', error, error);
            ajaxTree.jstree("refresh");
          }
        });


      })
      .jstree({
        core: {
          check_callback: true,
          data: {
            url: url,
            dataType: 'json',
            data: function (node) {
              return {
                id: node.id
              };
            }
          }
        },
        // plugins: ['types', 'state'],
        plugins: [
          "checkbox",
          "dnd",
          "types",
          "changed",
        ],
        "dnd": {
          "is_draggable": function (node) {
            if (node[0].state.disabled) {
              makeAlert('warning', thisIsNotDraggable, error);
              return false;
            }
            return true;
          }
        },
        types: {
          default: {
            icon: 'far fa-folder'
          },
          main: {
            icon: 'fas fa-layer-group'
          },
          sector: {
            icon: 'fas fas fa-sitemap'
          },
          department: {
            icon: 'fas fa-code-branch'
          },
          manager: {
            icon: 'fas fa-users'
          },
          employee: {
            icon: 'fas fa-user'
          }
        }
      });
  }
});
