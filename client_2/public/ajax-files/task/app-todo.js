/*=========================================================================================
    File Name: app-todo.js
    Description: app-todo
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

'use strict';

$(function () {
  var taskTitle,
    flatPickrDueDate = $('.task-due-date'),
    flatPickrStartDate = $('.task-start-date'),
    newTaskModal = $('.sidebar-todo-modal'),
    newTaskForm = $('#form-modal-todo'),
    favoriteStar = $('.todo-item-favorite'),
    modalTitle = $('.modal-title'),
    addBtn = $('.add-todo-item'),
    addTaskBtn = $('#addn'),
    updateTodoItem = $('.update-todo-item'),
    updateBtns = $('.update-btn'),
    taskDesc = $('#task-desc'),
    taskAssignSelect = $('#task-assigned'),
    taskAssignSelectTeam = $('#task-assigned-team'),
    creatorTaskStatusSelect = $('#creator-task-status'),
    assigneeTaskStatusSelect = $('#assignee-task-status'),
    taskTag = $('#task-tag'),
    overlay = $('.body-content-overlay'),
    menuToggle = $('.menu-toggle'),
    sidebarToggle = $('.sidebar-toggle'),
    sidebarLeft = $('.sidebar-left'),
    sidebarMenuList = $('.sidebar-menu-list'),
    todoFilter = $('#todo-search'),
    sortAsc = $('.sort-asc'),
    sortDesc = $('.sort-desc'),
    todoTaskList = $('.todo-task-list'),
    todoTaskListWrapper = $('.todo-task-list-wrapper'),
    listItemFilter = $('.list-group-filters'),
    noResults = $('.no-results'),
    checkboxId = 100,
    isRtl = $('html').attr('data-textdirection') === 'rtl';

  var priorities = {
    'No Priority': 'dark',
    'Low': 'success',
    'Normal': 'info',
    'High': 'warning',
    'Urgent': 'danger',
  };

  var assetPath = '../../../app-assets/';
  if ($('body').attr('data-framework') === 'laravel') {
    assetPath = $('body').attr('data-asset-path');
  }

  // if it is not touch device
  if (!$.app.menu.is_touch_device()) {
    if (sidebarMenuList.length > 0) {
      var sidebarListScrollbar = new PerfectScrollbar(sidebarMenuList[0], {
        theme: 'dark'
      });
    }
    if (todoTaskListWrapper.length > 0) {
      var taskListScrollbar = new PerfectScrollbar(todoTaskListWrapper[0], {
        theme: 'dark'
      });
    }
  }
  // if it is a touch device
  else {
    sidebarMenuList.css('overflow', 'scroll');
    todoTaskListWrapper.css('overflow', 'scroll');
  }

  // Add class active on click of sidebar filters list
  if (listItemFilter.length) {
    listItemFilter.find('a').on('click', function () {
      if (listItemFilter.find('a').hasClass('active')) {
        listItemFilter.find('a').removeClass('active');
      }
      $(this).addClass('active');
    });
  }

  // Init D'n'D
  var dndContainer = document.getElementById('todo-task-list');
  if (typeof dndContainer !== undefined && dndContainer !== null) {
    dragula([dndContainer], {
      moves: function (el, container, handle) {
        return handle.classList.contains('drag-icon');
      }
    });
  }

  // Main menu toggle should hide app menu
  if (menuToggle.length) {
    menuToggle.on('click', function (e) {
      sidebarLeft.removeClass('show');
      overlay.removeClass('show');
    });
  }

  // Todo sidebar toggle
  if (sidebarToggle.length) {
    sidebarToggle.on('click', function (e) {
      e.stopPropagation();
      sidebarLeft.toggleClass('show');
      overlay.addClass('show');
    });
  }

  // On Overlay Click
  if (overlay.length) {
    overlay.on('click', function (e) {
      sidebarLeft.removeClass('show');
      overlay.removeClass('show');
      $(newTaskModal).modal('hide');
    });
  }

  // Task Assign Select2
  if (taskAssignSelect.length) {
    taskAssignSelect.wrap('<div class="position-relative"></div>');
    taskAssignSelect.select2({
      dropdownParent: taskAssignSelect.parent(),
      escapeMarkup: function (es) {
        return es;
      }
    });
  }

  // Task Assign Team Select2
  if (taskAssignSelectTeam.length) {
    taskAssignSelectTeam.wrap('<div class="position-relative"></div>');
    taskAssignSelectTeam.select2({
      dropdownParent: taskAssignSelectTeam.parent(),
      escapeMarkup: function (es) {
        return es;
      }
    });
  }


  // Creator Task Status Select2
  if (creatorTaskStatusSelect.length) {
    creatorTaskStatusSelect.wrap('<div class="position-relative"></div>');
    creatorTaskStatusSelect.select2({
      dropdownParent: creatorTaskStatusSelect.parent(),
      escapeMarkup: function (es) {
        return es;
      }
    });
  }

  // Assignee Task Status Select2
  if (assigneeTaskStatusSelect.length) {
    assigneeTaskStatusSelect.wrap('<div class="position-relative"></div>');
    assigneeTaskStatusSelect.select2({
      dropdownParent: assigneeTaskStatusSelect.parent(),
      escapeMarkup: function (es) {
        return es;
      }
    });
  }

  // Task Tags
  if (taskTag.length) {
    taskTag.wrap('<div class="position-relative"></div>');
    taskTag.select2({
    });
  }
  let pickerObject = {
    dateFormat: 'Y-m-d',
    defaultDate: 'today',
    onReady: function (selectedDates, dateStr, instance) {
      if (instance.isMobile) {
        $(instance.mobileInput).attr('step', null);
      }
    }
  };

  if (!createdByMe) {
    delete pickerObject.defaultDate;
  }
  // Flat Picker
  if (flatPickrStartDate.length) {
    flatPickrStartDate.flatpickr(pickerObject);
  }

  if (createdByMe) {
    const currentData = new Date();
    pickerObject.defaultDate = currentData.setDate(currentData.getDate() + 10);
  }

  if (flatPickrDueDate.length) {
    flatPickrDueDate.flatpickr(pickerObject);
  }
  // Todo Description Editor
  if (taskDesc.length) {
    var todoDescEditor = new Quill('#task-desc', {
      bounds: '#task-desc',
      modules: {
        formula: true,
        syntax: true,
        toolbar: '.desc-toolbar'
      },
      placeholder: lang['Description'],
      theme: 'snow'
    });
  }

  // On add new item button click, clear sidebar-right field fields
  if (addTaskBtn.length) {
    addTaskBtn.on('click', function (e) {
      $('#task_assigned_team_container').addClass('d-none');
      $('#task_assigned_container').removeClass('d-none');
      $('#Team').change(function () {
        if ($(this).is(':checked')) {
          $('#task_assigned_container').addClass('d-none');
          $('#task_assigned_team_container').removeClass('d-none');
        }
      });

      $('#Employee').change(function () {
        if ($(this).is(':checked')) {
          $('#task_assigned_team_container').addClass('d-none');
          $('#task_assigned_container').removeClass('d-none');
        }
      });
      $('#creator-task-status-container').addClass('d-none');
      $('#chat-container').addClass('d-none');
      addBtn.removeClass('d-none');
      updateBtns.addClass('d-none');
      modalTitle.text(lang['AddNewTask']);
      // newTaskModal.modal('show');
      sidebarLeft.removeClass('show');
      overlay.removeClass('show');
      newTaskModal.find('.new-todo-item-title').val('');
      var quill_editor = taskDesc.find('.ql-editor');
      quill_editor[0].innerHTML = '';
    });
  }
  // newTaskModal.on('hidden.bs.modal', function (e) {
  //   reset();
  // });
  // Add New ToDo List Item

  // To add new todo form
  if (newTaskForm.length) {
    newTaskForm.validate({
      ignore: '.ql-container *', // ? ignoring quill editor icon click, that was creating console error
      rules: {
        title: {
          required: true
        },
        assignee_type: {
          required: true
        },
        'task-assigned': {
          required: true
        },
        'task-due-date': {
          required: true
        },
        'task-start-date': {
          required: true
        },
        'task-priority': {
          required: true
        }
      }
    });

    newTaskForm.on('submit', function (e) {
      e.preventDefault();
      var isValid = newTaskForm.valid();
      if (isValid) {
        if (todoDescEditor.getLength() == 1) {
          $('.error-description').empty();
          $('.error-description').append('This field is required.').css('display', 'inline-block');
          return;
        }
        $('[name="description"]').val($("#task-desc .ql-editor").html());
        var formData = new FormData(this);

        $.ajax({
          url: URLs['create']
          , type: "POST"
          , data: formData
          ,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }

          , contentType: false
          , processData: false
          , success: function (data) {
            if (data.status) {
              makeAlert('success', data.message, lang['success']);
              if (data.reload)
                location.reload();
              // checkboxId++;
              // var todoBadge = '';

              // var todoTitle = $('.sidebar-todo-modal .new-todo-item-title').val();
              // var date = $('.sidebar-todo-modal .task-due-date').val(),
              //   selectedDate = new Date(date),
              //   month = new Intl.DateTimeFormat('en', { month: 'short' }).format(selectedDate),
              //   day = new Intl.DateTimeFormat('en', { day: '2-digit' }).format(selectedDate),
              //   todoDate = month + ' ' + day;

              // // Badge calculation loop
              // var selected = $('.task-tag').val();
              // var badgeColor = {
              //   Low: 'success',
              //   Normal: 'info',
              //   High: 'warning',
              //   Urgent: 'danger',
              //   'No Priority': 'dark'
              // };

              // todoBadge +=
              //   '<span class="badge rounded-pill badge-light-' + badgeColor[selected] + ' me-50">' + lang['TaskPriorities'][selected] + '</span>';
              // // HTML Output
              // if (todoTitle != '') {
              //   $(todoTaskList).prepend(
              //     '<li class="todo-item">' +
              //     '<div class="todo-title-wrapper">' +
              //     '<div class="todo-title-area">' +
              //     feather.icons['more-vertical'].toSvg({ class: 'drag-icon' }) +
              //     '<div class="title-wrapper">' +
              //     '<div class="form-check">' +
              //     '<input type="checkbox" class="form-check-input" id="customCheck' +
              //     checkboxId +
              //     '" />' +
              //     '<label class="form-check-label" for="customCheck' +
              //     checkboxId +
              //     '"></label>' +
              //     '</div>' +
              //     '<span class="todo-title">' +
              //     todoTitle +
              //     '</span>' +
              //     '</div>' +
              //     '</div>' +
              //     '<div class="todo-item-action">' +
              //     '<span class="badge-wrapper me-1">' +
              //     todoBadge +
              //     '</span>' +
              //     '<small class="text-nowrap text-muted me-1">' +
              //     todoDate +
              //     '</small>' +
              //     '<div class="avatar  bg-light-primary">' +
              //     `<div class="avatar-content" data-bs-toggle="tooltip" data-bs-placement="top"
              //     title=""
              //     data-bs-original-title="User: Ahmed Fawzy">A.F</div>` +
              //     '</div>' +
              //     '</div>' +
              //     '</div>' +
              //     '</li>'
              //   );
              // }
              $(newTaskModal).modal('hide');
              overlay.removeClass('show');
            } else {
              showError(data['errors']);
            }
          }
          , error: function (response, data) {
            const responseData = response.responseJSON;
            makeAlert('error', responseData.message, lang['error']);
            showError(responseData.errors);
          }
        });
      }
    });
  }

  // Task checkbox change (completed status)
  // todoTaskListWrapper.on('change', '.form-check', function (event) {
  //   var $this = $(this).find('input');
  //   const _that = this;
  //   let url = URLs['changeCompleteStatus'];
  //   $.ajax({
  //     url: url
  //     , type: "PUT"
  //     , data: {
  //       id: $(_that).find('input').data('id'),
  //       completed: $(_that).find('input').prop('checked')
  //     }
  //     , headers: {
  //       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  //     }
  //     , success: function (response) {
  //       if (response.status) {
  //         makeAlert('success', response.message, lang['success']);
  //         $(that).parent().remove();
  //         appendLog(response.data.log);
  //       }
  //     }
  //     , error: function (response, data) {
  //       let responseData = response.responseJSON;
  //       makeAlert('error', responseData.message, lang['error']);
  //     }
  //   });

  //   if ($this.prop('checked')) {
  //     $this.closest('.todo-item').addClass('completed');
  //   } else {
  //     $this.closest('.todo-item').removeClass('completed');
  //   }
  // });
  // todoTaskListWrapper.on('click', '.form-check', function (event) {
  //   event.stopPropagation();
  // });

  // To open todo list item modal on click of item
  $(document).on('click', '#click_edit , .todo-item', function (e) {
    // get to show update
    ShowModalEditDepartment($(this).data('id'));

    addBtn.addClass('d-none');
    if (createdByMe)
      updateBtns.removeClass('d-none');
    // if ($(this).hasClass('completed')) {
    //   modalTitle.html(
    //     `<button type="button" class="btn btn-sm btn-outline-success complete-todo-item waves-effect waves-float waves-light" data-bs-dismiss="modal">${lang['Completed']}</button>`
    //   );
    // } else {
    //   modalTitle.html(
    //     `<button type="button" class="btn btn-sm btn-outline-secondary complete-todo-item waves-effect waves-float waves-light" data-bs-dismiss="modal">${lang['NotCompleted']}</button>`
    //   );
    // }
  });

  // Updating Data Values to Fields
  if (updateTodoItem.length) {
    updateTodoItem.on('click', function (e) {
      var isValid = newTaskForm.valid();
      e.preventDefault();
      if (isValid) {
        if (todoDescEditor.getLength() == 1) {
          $('.error-description').empty();
          $('.error-description').append('This field is required.').css('display', 'inline-block');
          return;
        }
        $('[name="description"]').val($("#task-desc .ql-editor").html());
        // Update task ajax start
        var formData = new FormData(document.querySelector('#form-modal-todo'));
        formData.append('_method', 'PUT');

        let url = URLs['update'];
        $.ajax({
          url: url
          , type: "POST"
          , data: formData
          , contentType: false
          , processData: false
          , headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
          , success: function (data) {
            if (data.status) {
              makeAlert('success', data.message, lang['success']);
              let taskRow = $(`#todo-item-${data.task.id}`);
              // taskRow.find('.todo-title').text(data.task.title)
              // taskRow.find('.todo-item-action .badge-wrapper .badge.rounded-pill').removeClass().addClass(`badge rounded-pill badge-light-${priorities[`${data.task.priority}`]}`).text(data.task.priority)
              // let selectedDate = new Date(data.task.due_date),
              //   month = new Intl.DateTimeFormat('en', { month: 'long' }).format(selectedDate),
              //   day = new Intl.DateTimeFormat('en', { day: '2-digit' }).format(selectedDate),
              //   todoDate = month + ' ' + (+day + 1);
              //   console.log(month);
              //   console.log(day);

              // taskRow.find('.todo-item-action .text-nowrap.text-muted.me-1').text(todoDate)
              $(newTaskModal).modal('hide');

              if (data.reload)
                location.reload();
            } else {
              showError(data['errors']);
            }
          }
          , error: function (response, data) {
            let responseData = response.responseJSON;
            makeAlert('error', responseData.message, lang['error']);
            showError(responseData.errors);
          }
        });
        // Update task ajax End
      }
    });
  }

  // Sort Ascending
  if (sortAsc.length) {
    sortAsc.on('click', function () {
      todoTaskListWrapper
        .find('li')
        .sort(function (a, b) {
          return $(b).find('.todo-title').text().toUpperCase() < $(a).find('.todo-title').text().toUpperCase() ? 1 : -1;
        })
        .appendTo(todoTaskList);
    });
  }
  // Sort Descending
  if (sortDesc.length) {
    sortDesc.on('click', function () {
      todoTaskListWrapper
        .find('li')
        .sort(function (a, b) {
          return $(b).find('.todo-title').text().toUpperCase() > $(a).find('.todo-title').text().toUpperCase() ? 1 : -1;
        })
        .appendTo(todoTaskList);
    });
  }

  // Filter task
  if (todoFilter.length) {
    todoFilter.on('keyup', function () {
      var value = $(this).val().toLowerCase();
      if (value !== '') {
        $('.todo-item').filter(function () {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
        var tbl_row = $('.todo-item:visible').length; //here tbl_test is table name

        //Check if table has row or not
        if (tbl_row == 0) {
          if (!$(noResults).hasClass('show')) {
            $(noResults).addClass('show');
          }
        } else {
          $(noResults).removeClass('show');
        }
      } else {
        // If filter box is empty
        $('.todo-item').show();
        if ($(noResults).hasClass('show')) {
          $(noResults).removeClass('show');
        }
      }
    });
  }

  // For chat sidebar on small screen
  if ($(window).width() > 992) {
    if (overlay.hasClass('show')) {
      overlay.removeClass('show');
    }
  }

  // Delete supporting documentation start
  $(document).on('click', '.delete_supporting_documentation', function () {
    showModalDeleteTaskFile($(this).data('id'), $(this).data('taskId'), this);
  });

  // Delete supporting documentation End

  // Show delete alert modal Start
  function showModalDeleteTaskFile(id, taskId, that) {
    Swal.fire({
      title: lang['confirmDeleteFileMessage']
      , text: lang['revert']
      , icon: 'question'
      , showCancelButton: true
      , confirmButtonText: lang['confirmDelete']
      , cancelButtonText: lang['cancel']
      , customClass: {
        confirmButton: 'btn btn-relief-success ms-1'
        , cancelButton: 'btn btn-outline-danger ms-1'
      }
      , buttonsStyling: false
    }).then(function (result) {
      if (result.value) {
        DeleteFile(id, taskId, that)
      }
    });
  }
  // Show delete alert modal End

  // Download supporting documentation start
  $(document).on('click', '.supporting_documentation', function () {
    const form = $('#download-file-form');
    form.find('[name="id"').val($(this).data('id'));
    form.find('[name="task_id"').val($(this).data('taskId'));

    form.trigger('submit');
  });
  // Download supporting documentation End

  // Delete supporting documentation start
  function DeleteFile(id, taskId, that) {
    let url = URLs['deleteFile'];
    $.ajax({
      url: url
      , type: "DELETE"
      , data: {
        id: id,
        task_id: taskId,
      }
      , headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
      , success: function (response) {
        if (response.status) {
          makeAlert('success', response.message, lang['success']);
          $(that).parent().remove();
          appendLog(response.data.log);
        }
      }
      , error: function (response, data) {
        let responseData = response.responseJSON;
        makeAlert('error', responseData.message, lang['error']);
      }
    });
  }
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

  // Show modal for editing
  function ShowModalEditDepartment(id) {
    // Show change status select
    $('#creator-task-status-container').removeClass('d-none');
    $('#chat-container').removeClass('d-none');

    let url = createdByMe ? URLs['edit'] : URLs['show'];
    url = url.replace(':id', id);
    $.ajax({
      url: url
      , type: "GET"
      , headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
      , success: function (response) {
        if (response.status) {
          const editForm = $("#form-modal-todo");

          editForm.find(`select[name='task_cat'] option`).attr('selected', false);

          if (response.data.category_task) {
            const categoryId = response.data.category_task.id;
            const categoryName = response.data.category_task.name;

            editForm.find(`select[name='task_cat'] option[value='${categoryId}']`).attr('selected', true).trigger('change');
          }

          let title = `<button type="button" class="btn btn-sm btn-outline-success complete-todo-item waves-effect waves-float waves-light" data-bs-dismiss="modal">${lang[response.data.status]}</button>`;

          if (['In Progress', 'Completed'].includes(response.data.status)) {
            title += ` ${lang['by']} <span class="badge rounded-pill badge-light-primary">${response.data.action_by}</span>`;
          }

          if (response.data.status == 'Completed') {
            title += ` ${lang['at']} <span class="badge rounded-pill badge-light-info">${response.data.completed_date}</span>`;
          } else if (response.data.status == 'Accepted') {
            title += ` ${lang['at']} <span class="badge rounded-pill badge-light-info">${response.data.accepted_date}</span>`;
          } else if (response.data.status == 'Open') {
            title += ` ${lang['at']} <span class="badge rounded-pill badge-light-info">${response.data.created_at}</span>`;
          }
          modalTitle.html(title)

          // modalTitle.html(
          //   `<button type="button" class="btn btn-sm btn-outline-success complete-todo-item waves-effect waves-float waves-light" data-bs-dismiss="modal">${lang[response.data.status]}</button> <span>${lang['by']} ${lang[response.data.status]}</span><p class="mt-1"></p>
          //   <button type="button" class=" btn btn-sm btn-outline-primary complete-todo-item waves-effect waves-float waves-light" data-bs-dismiss="modal">${lang[response.data.status]}</button> <span>${lang['by']} ${lang[response.data.status]}</span>`
          // );

          if (response.data.assignable_type == 'User') {
            $('[value="User"]').prop('checked', true);
            $('[value="User"]').trigger('change');
          } else {
            $('[value="Team"]').prop('checked', true);
            $('[value="Team"]').trigger('change');
          }

          // Start Assign task data to modal
          $('#assignee-change-status-form').find('input[name="id"]').val(id);
          editForm.find('input[name="id"]').val(id);
          editForm.find("input[name='title']").val(response.data.title);
          editForm.find("input[name='task-start-date']").val(response.data.start_date).flatpickr({
            dateFormat: 'Y-m-d',
            defaultDate: response.data.start_date,
            onReady: function (selectedDates, dateStr, instance) {
              if (instance.isMobile) {
                $(instance.mobileInput).attr('step', null);
              }
            }
          });
          editForm.find("input[name='task-due-date']").val(response.data.due_date).flatpickr({
            dateFormat: 'Y-m-d',
            defaultDate: response.data.due_date,
            onReady: function (selectedDates, dateStr, instance) {
              if (instance.isMobile) {
                $(instance.mobileInput).attr('step', null);
              }
            }
          });

          editForm.find("#display-describtion").html(response.data.description);
          var quill_editor = taskDesc.find('.ql-editor');
          if (quill_editor.length)
            quill_editor[0].innerHTML = response.data.description;
          $('.supporting_documentation_container .mitigation-files').remove();
          $('.supporting_documentation_container .text-danger').remove();

          if (response.data.files.length) {
            response.data.files.forEach(file => {
              $('.supporting_documentation_container').append(`
              <div class="mitigation-files" style="margin-top: 5px">
              <span class="badge bg-secondary supporting_documentation cursor-pointer" data-id="${file.id}" data-task-id="${response.data.id}">${file.display_name}</span>`
                +
                ((createdByMe) ?
                  `<span class="text-danger delete_supporting_documentation cursor-pointer mx-1" data-id="${file.id}" data-task-id="${response.data.id}"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span>`
                  : '')
                +
                `</div>
              `);
            });
          }
          else
            $('.supporting_documentation_container').append(`<span class="mx-2 text-danger">${lang['NONE']}</span>`);

          if (response.data.assignable_type == 'User') {
            editForm.find(`select[name='task-assigned'] option`).attr('selected', false);
            editForm.find(`select[name='task-assigned'] option[value='${response.data.assignable_id}']`)
              .attr('selected', true).trigger('change');
          } else {
            editForm.find(`select[name='task_assigned_team'] option`).attr('selected', false);
            editForm.find(`select[name='task_assigned_team'] option[value='${response.data.assignable_id}']`)
              .attr('selected', true).trigger('change');
          }
          editForm.find(`select[name='task-priority'] option[value='${response.data.priority}']`)
            .attr('selected', true).trigger('change');

          editForm.find(`select[name='task-status'] option`).attr('selected', false);
          editForm.find(`select[name='task-status'] option:first`).attr('selected', true).trigger('change');

          if (editForm.find(`select[name='task-status'] option[value='${response.data.status}']`).length) {
            editForm.find(`select[name='task-status'] option[value='${response.data.status}']`).attr('selected', true).trigger('change');
          }
          if ($(`#assignee-task-status`).length) {
            $(`#assignee-task-status option`).attr('selected', false);
            $(`#assignee-task-status option:first`).attr('selected', true).trigger('change');
            if ($(`#assignee-task-status option[value='${response.data.status}']`).length) {
              $(`#assignee-task-status option`).attr('selected', false);
              $(`#assignee-task-status option[value='${response.data.status}']`).attr('selected', true).trigger('change');
            }
          }

          addMessageToChat(response.data);
          $('.delete-todo-item').data('id', id);
          newTaskModal.modal('show');
          // End Assign task data to modal
        }
      }
      , error: function (response, data) {
        let responseData = response.responseJSON;
        makeAlert('error', responseData.message, lang['error']);
      }
    });
  }

  // function to show error validation 
  function showError(data) {
    $('.error').empty();
    $.each(data, function (key, value) {
      $('.error-' + key).empty();
      $('.error-' + key).append(value);
      $('.error-' + key).css('display', 'inline-block');
    });
  }

  // Delete task start
  $('.delete-todo-item').on('click', function () {
    showModalDeleteTask($(this).data('id'), $(this).data('taskId'), this);
  });

  // Delete task End

  // Show delete alert modal Start
  function showModalDeleteTask(id, taskId, that) {
    Swal.fire({
      title: lang['confirmDeleteRecordMessage']
      , text: lang['revert']
      , icon: 'question'
      , showCancelButton: true
      , confirmButtonText: lang['confirmDelete']
      , cancelButtonText: lang['cancel']
      , customClass: {
        confirmButton: 'btn btn-relief-success ms-1'
        , cancelButton: 'btn btn-outline-danger ms-1'
      }
      , buttonsStyling: false
    }).then(function (result) {
      if (result.value) {
        let url = URLs['delete'];
        url = url.replace(':id', id);
        $.ajax({
          url: url
          , type: "DELETE"
          , headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
          , success: function (data) {
            if (data.status) {
              makeAlert('success', data.message, lang['success']);
              location.reload();
            }
          }
          , error: function (response, data) {
            responseData = response.responseJSON;
            makeAlert('error', responseData.message, lang['error']);
          }
        });
      }
    });
  }

  // Handle event for assignee type changes
  $('[name="assignee_type"]').on('change', function () {
    // Reset old selection
    $('#task_assigned_container').find('select').prop('selectedIndex', 0).trigger('change');
    $('#task_assigned_team_container').find('select').prop('selectedIndex', 0).trigger('change');

    if ($(this).val() == 'User') {
      $('#task_assigned_container').removeClass('d-none');
      $('#task_assigned_team_container').addClass('d-none');

      if (newTaskForm.length) {
        $('[name="task_assigned_team').rules("remove", 'required');
        $('[name="task-assigned').rules("add", {
          required: true
        });
      }
    } else {
      $('#task_assigned_container').addClass('d-none');
      $('#task_assigned_team_container').removeClass('d-none');
      if (newTaskForm.length) {
        if (newTaskForm.length) {
          $('[name="task-assigned').rules("remove", 'required');
          $('[name="task_assigned_team').rules("add", {
            required: true
          });
        }
      }
    }
  });

  // Update assignee task status
  $('#update-change-status-btn').on('click', function () {
    var formData = new FormData(document.querySelector('#assignee-change-status-form'));
    $.ajax({
      url: URLs['assigneeUpdateStatus']
      , type: "POST"
      , data: formData
      , contentType: false
      , processData: false
      , success: function (response) {
        if (response.data.status) {
          if (response.data.completed) {
            $(`#todo-item-${$('#assignee-change-status-form [name="id"]').val()} [type="checkbox"]`).prop('checked', true);
          } else {
            $(`#todo-item-${$('#assignee-change-status-form [name="id"]').val()} [type="checkbox"]`).prop('checked', false);
          }
          makeAlert('success', response.message, lang['success']);
          var oTable = $('#todo-task-table').DataTable();
          $(newTaskModal).modal('hide');
          overlay.removeClass('show');
          oTable.ajax.reload();
          if (response.reload)
            // location.reload();
            $(newTaskModal).modal('hide');

          overlay.removeClass('show');
        } else {
          showError(data['errors']);
        }
      }
      , error: function (response, data) {
        const responseData = response.responseJSON;
        makeAlert('error', responseData.message, lang['error']);
        showError(responseData.errors);
      }
    });
  });

  // Add filter by tags
  $('.list-group-item-action').on('click', function () {
    if ($('#todo-search').val() == $(this).data('text')) {
      $('#todo-search').val('').trigger('keyup');
    } else {
      $('#todo-search').val($(this).data('text')).trigger('keyup');
    }
  });

});
// Show delete alert modal End

$(window).on('resize', function () {
  // remove show classes from sidebar and overlay if size is > 992
  if ($(window).width() > 992) {
    if ($('.body-content-overlay').hasClass('show')) {
      $('.sidebar-left').removeClass('show');
      $('.body-content-overlay').removeClass('show');
      $('.sidebar-todo-modal').modal('hide');
    }
  }
});

function addMessageToChat(task) {
  const notes = task.notes
  // Reset chat content
  $('.chats').html('');
  notes.forEach(note => {
    if (/\S/.test(note.note)) {
      // note from other user
      if (user_id != note.user_id) {
        // if first chat note to be added
        if ($('.chat:last-child').length == 0) {
          $('.chats').append(
            `<div class="chat chat-left user${note.user_id}">
                  <div class="chat-avatar">
                      <span class="avatar box-shadow-1 cursor-pointer ` + (note.user_id == task.created_by ? 'bg-light-success' : 'bg-light-primary') + ` ">
                          <div class="avatar-content" data-bs-toggle="tooltip" data-bs-placement="top" title="${lang['user']}: ${note.user_name}" data-bs-original-title="${lang['user']}">${note.custom_user_name}</div>
                      </span>
                  </div>
                  <div class="chat-body">
                  </div>
                </div>`
          );
        }
        if ($('.chat:last-child').hasClass('chat-left') && $('.chat:last-child').hasClass(`user${note.user_id}`)) {
          $('.chat:last-child .chat-body').append(
            `<div class="chat-content">`
            + (note.type == 't' ? `<p>${note.note}</p>` : `<p class="download-note-file cursor-pointer" data-id='${note.id}' data-task-id='${task.id}'><u>${note.note}</u></p>`) +
            `<p style="text-align: right"><small><b>${note.created_at}</b></small></p>
              </div>`
          );
        } else {
          $('.chats').append(
            `<div class="chat chat-left user${note.user_id}">
                  <div class="chat-avatar">
                      <span class="avatar box-shadow-1 cursor-pointer ` + (note.user_id == task.created_by ? 'bg-light-success' : 'bg-light-primary') + ` ">
                          <div class="avatar-content" data-bs-toggle="tooltip" data-bs-placement="top" title="${lang['user']}: ${note.user_name}" data-bs-original-title="${lang['user']}">${note.custom_user_name}</div>
                      </span>
                  </div>
                  <div class="chat-body">
                        <div class="chat-content">`
            + (note.type == 't' ? `<p>${note.note}</p>` : `<p class="download-note-file cursor-pointer" data-id='${note.id}' data-task-id='${task.id}'><u>${note.note}</u></p>`) +
            `<p style="text-align: right"><small><b>${note.created_at}</b></small></p>
                        </div>
                    </div>
                </div>`
          );
        }
      } else {
        // Note from me
        // if first chat note to be added
        if ($('.chat:last-child').length == 0) {
          $('.chats').append(
            `<div class="chat">
                <div class="chat-avatar">
                    <span class="avatar box-shadow-1 cursor-pointer ` + (note.user_id == task.created_by ? 'bg-light-success' : 'bg-light-primary') + ` ">
                        <div class="avatar-content" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="${lang['user']}: ${note.user_name}" data-bs-original-title="${lang['user']}">${note.custom_user_name}</div>
                    </span>
                </div>
                <div class="chat-body">
                </div>
              </div>`
          );
        }

        if ($('.chat:last-child').hasClass('chat-left')) {
          $('.chats').append(
            `<div class="chat">
                <div class="chat-avatar">
                    <span class="avatar box-shadow-1 cursor-pointer ` + (note.user_id == task.created_by ? 'bg-light-success' : 'bg-light-primary') + ` ">
                        <div class="avatar-content" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="${lang['user']}: ${note.user_name}" data-bs-original-title="${lang['user']}">${note.custom_user_name}</div>
                    </span>
                </div>
                
                <div class="chat-body">
                    <div class="chat-content">`
            + (note.type == 't' ? `<p>${note.note}</p>` : `<p class="download-note-file cursor-pointer" data-id='${note.id}' data-task-id='${task.id}'><u>${note.note}</u></p>`) +
            `<p style="text-align: right"><small><b>${note.created_at}</b></small></p>
                    </div>
                </div>
              </div>`
          );

        } else {
          $('.chat:last-child .chat-body').append(
            `<div class="chat-content">`
            + (note.type == 't' ? `<p>${note.note}</p>` : `<p class="download-note-file cursor-pointer" data-id='${note.id}' data-task-id='${task.id}'><u>${note.note}</u></p>`) +
            `<p style="text-align: right"><small><b>${note.created_at}</b></small></p>
              </div>`
          );
        }
      }
      $('.message').val('');
      $('.user-chats').scrollTop($('.user-chats > .chats').height());
    }
  });
}
function reset() {
  $('#new-task-modal form')[0].reset(); // Reset the entire form inside the modal
  $('#new-task-modal').find('.mitigation-files').empty(); // Clear any additional content
  $('#new-task-modal').find('.ql-editor').html('').trigger('change'); // Clear Quill editor content
}
