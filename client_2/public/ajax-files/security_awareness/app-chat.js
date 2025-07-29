/*=========================================================================================
    File Name: app-chat.js
    Description: Chat app js
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

'use strict';
var sidebarToggle = $('.sidebar-toggle'),
  overlay = $('.body-content-overlay'),
  sidebarContent = $('.sidebar-content');

// Chat sidebar toggle
function sidebarToggleFunction() {
  if (sidebarToggle.length) {
    sidebarToggle.on('click', function () {
      sidebarContent.addClass('show');
      overlay.addClass('show');
    });
  }
}
$(function () {
  var chatUsersListWrapper = $('.chat-application .chat-user-list-wrapper'),
    profileSidebar = $('.chat-application .chat-profile-sidebar'),
    profileSidebarArea = $('.chat-application .profile-sidebar-area'),
    profileToggle = $('.chat-application .sidebar-profile-toggle'),
    userProfileToggle = $('.chat-application .user-profile-toggle'),
    userProfileSidebar = $('.user-profile-sidebar'),
    statusRadio = $('.chat-application .user-status input:radio[name=userStatus]'),
    userChats = $('.user-chats'),
    chatsUserList = $('.chat-users-list'),
    chatList = $('.chat-list'),
    contactList = $('.contact-list'),
    closeIcon = $('.chat-application .close-icon'),
    sidebarCloseIcon = $('.chat-application .sidebar-close-icon'),
    menuToggle = $('.chat-application .menu-toggle'),
    speechToText = $('.speech-to-text'),
    chatSearch = $('.chat-application #chat-search');

  // init ps if it is not touch device
  if (!$.app.menu.is_touch_device()) {
    // Chat user list
    if (chatUsersListWrapper.length > 0) {
      var chatUserList = new PerfectScrollbar(chatUsersListWrapper[0]);
    }

    // Admin profile left
    if (userProfileSidebar.find('.user-profile-sidebar-area').length > 0) {
      var userScrollArea = new PerfectScrollbar(userProfileSidebar.find('.user-profile-sidebar-area')[0]);
    }

    // Chat area
    if (userChats.length > 0) {
      var chatsUser = new PerfectScrollbar(userChats[0], {
        wheelPropagation: false
      });
    }

    // User profile right area
    if (profileSidebarArea.length > 0) {
      var user_profile = new PerfectScrollbar(profileSidebarArea[0]);
    }
  } else {
    chatUsersListWrapper.css('overflow', 'scroll');
    userProfileSidebar.find('.user-profile-sidebar-area').css('overflow', 'scroll');
    userChats.css('overflow', 'scroll');
    profileSidebarArea.css('overflow', 'scroll');

    // on user click sidebar close in touch devices
    $(chatsUserList)
      .find('li')
      .on('click', function () {
        $(sidebarContent).removeClass('show');
        $(overlay).removeClass('show');
      });
  }

  // Chat Profile sidebar & overlay toggle
  if (profileToggle.length) {
    profileToggle.on('click', function () {
      profileSidebar.addClass('show');
      overlay.addClass('show');
    });
  }

  // On Profile close click
  if (closeIcon.length) {
    closeIcon.on('click', function () {
      profileSidebar.removeClass('show');
      userProfileSidebar.removeClass('show');
      if (!sidebarContent.hasClass('show')) {
        overlay.removeClass('show');
      }
    });
  }

  // On sidebar close click
  if (sidebarCloseIcon.length) {
    sidebarCloseIcon.on('click', function () {
      sidebarContent.removeClass('show');
      overlay.removeClass('show');
    });
  }

  // User Profile sidebar toggle
  if (userProfileToggle.length) {
    userProfileToggle.on('click', function () {
      userProfileSidebar.addClass('show');
      overlay.addClass('show');
    });
  }

  // On overlay click
  if (overlay.length) {
    overlay.on('click', function () {
      sidebarContent.removeClass('show');
      overlay.removeClass('show');
      profileSidebar.removeClass('show');
      userProfileSidebar.removeClass('show');
    });
  }

  // Add class active on click of Chat users list
  if (chatUsersListWrapper.find('ul li').length) {
    chatUsersListWrapper.find('ul li').on('click', function () {
      var $this = $(this),
        startArea = $('.start-chat-area'),
        activeChat = $('.active-chat');

      if (chatUsersListWrapper.find('ul li').hasClass('active')) {
        chatUsersListWrapper.find('ul li').removeClass('active');
      }

      $this.addClass('active');
      $this.find('.badge').remove();

      if (chatUsersListWrapper.find('ul li').hasClass('active')) {
        startArea.addClass('d-none');
        activeChat.removeClass('d-none');
      } else {
        startArea.removeClass('d-none');
        activeChat.addClass('d-none');
      }
    });
  }

  // auto scroll to bottom of Chat area
  chatsUserList.find('li').on('click', function () {
    userChats.animate({ scrollTop: userChats[0].scrollHeight }, 400);
  });

  // Main menu toggle should hide app menu
  if (menuToggle.length) {
    menuToggle.on('click', function (e) {
      sidebarContent.removeClass('show');
      overlay.removeClass('show');
      profileSidebar.removeClass('show');
      userProfileSidebar.removeClass('show');
    });
  }

  if ($(window).width() < 992) {
    sidebarToggleFunction();
  }

  // Filter
  if (chatSearch.length) {
    chatSearch.on('keyup', function () {
      var value = $(this).val().toLowerCase();
      if (value !== '') {
        // filter chat list
        chatList.find('li:not(.no-results)').filter(function () {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
        // filter contact list
        contactList.find('li:not(.no-results)').filter(function () {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
        var chat_tbl_row = chatList.find('li:not(.no-results):visible').length,
          contact_tbl_row = contactList.find('li:not(.no-results):visible').length;

        // check if chat row available
        if (chat_tbl_row == 0) {
          chatList.find('.no-results').addClass('show');
        } else {
          if (chatList.find('.no-results').hasClass('show')) {
            chatList.find('.no-results').removeClass('show');
          }
        }

        // check if contact row available
        if (contact_tbl_row == 0) {
          contactList.find('.no-results').addClass('show');
        } else {
          if (contactList.find('.no-results').hasClass('show')) {
            contactList.find('.no-results').removeClass('show');
          }
        }
      } else {
        // If filter box is empty
        chatsUserList.find('li').show();
        if (chatUsersListWrapper.find('.no-results').hasClass('show')) {
          chatUsersListWrapper.find('.no-results').removeClass('show');
        }
      }
    });
  }

  if (speechToText.length) {
    // Speech To Text
    var SpeechRecognition = SpeechRecognition || webkitSpeechRecognition;
    if (SpeechRecognition !== undefined && SpeechRecognition !== null) {
      var recognition = new SpeechRecognition(),
        listening = false;
      speechToText.on('click', function () {
        var $this = $(this);
        recognition.onspeechstart = function () {
          listening = true;
        };
        if (listening === false) {
          recognition.start();
        }
        recognition.onerror = function (event) {
          listening = false;
        };
        recognition.onresult = function (event) {
          $this.closest('.form-send-message').find('.message').val(event.results[0][0].transcript);
        };
        recognition.onspeechend = function (event) {
          listening = false;
          recognition.stop();
        };
      });
    }
  }
});

// Window Resize
$(window).on('resize', function () {
  sidebarToggleFunction();
  if ($(window).width() > 992) {
    if ($('.chat-application .body-content-overlay').hasClass('show')) {
      $('.app-content .sidebar-left').removeClass('show');
      $('.chat-application .body-content-overlay').removeClass('show');
    }
  }

  // Chat sidebar toggle
  if ($(window).width() < 991) {
    if (
      !$('.chat-application .chat-profile-sidebar').hasClass('show') ||
      !$('.chat-application .sidebar-content').hasClass('show')
    ) {
      $('.sidebar-content').removeClass('show');
      $('.body-content-overlay').removeClass('show');
    }
  }
});

// Add message to chat - function call on form submit
function enterChat(editFormSelector) {
  const url = URLs['sendNote'];
  const editForm = $(editFormSelector);
  const message = editForm.find('.message').val();

  if (/\S/.test(message)) {
    $.ajax({
      url: url
      , type: "POST"
      , data: {
        security_awareness_id: editForm.find('input[name="id"]').val(),
        note: message
      }
      , headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
      , success: function (response) {
        if (response.status) {
          makeAlert('success', response.message, lang['success']);
          // if first chat note to be added
          if ($('.chat:last-child').length == 0) {
            $('.chats').append(
              `<div class="chat">
                    <div class="chat-avatar">
                        <span class="avatar box-shadow-1 cursor-pointer ` + (response.data.note.user_id == response.data.security_awareness.created_by ? 'bg-light-success' : 'bg-light-primary') + ` ">
                            <div class="avatar-content" data-bs-toggle="tooltip" data-bs-placement="top" title="${lang['user']} ${userName}" data-bs-original-title="${lang['user']}">${customUserName}</div>
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
              <span class="avatar box-shadow-1 cursor-pointer ` + (response.data.note.user_id == response.data.security_awareness.created_by ? 'bg-light-success' : 'bg-light-primary') + ` ">
                  <div class="avatar-content" data-bs-toggle="tooltip" data-bs-placement="top"
                      title="${lang['user']} ${userName}"
                      data-bs-original-title="${lang['user']}">${customUserName}</div>
              </span>
          </div>

          <div class="chat-body">
              <div class="chat-content">
                  <p>${message}</p>
                  <p style="text-align: right"><small><b>${response.data.note.created_at}</b></small></p>
              </div>
          </div>
        </div>`
            );

          } else {
            $('.chat:last-child .chat-body').append(
              `<div class="chat-content">
            <p>${message}</p>
            <p style="text-align: right"><small><b>${response.data.note.created_at}</b></small></p>
          </div>`
            );
          }
          $('.message').val('');
          $('.user-chats').scrollTop($('.user-chats > .chats').height());
        }
      }
      , error: function (response, data) {
        const responseData = response.responseJSON;
        makeAlert('error', responseData.message, lang['error']);
      }
    });
  }

  let attachSecurityAwarenessSelector = '';
  if (editFormSelector == '#edit-security-awareness')
    attachSecurityAwarenessSelector = '#attach-doc-edit-security-awareness';
  else if (editFormSelector == '#show-security-awareness')
    attachSecurityAwarenessSelector = '#attach-doc-show-security-awareness';


  if ($(attachSecurityAwarenessSelector).length && $(attachSecurityAwarenessSelector).val()) {

    editForm.find('.chat-app-form [name="security_awareness_id"]').val(editForm.find('input[name="id"]').val());

    var formData = new FormData(document.querySelector(`${editFormSelector} .chat-app-form`));

    $.ajax({
      url: URLs['sendNoteFile']
      , type: "POST"
      , data: formData
      , contentType: false
      , processData: false
      , success: function (response) {
        if (response.status) {
          makeAlert('success', response.message, lang['success']);

          // if first chat note to be added
          if ($('.chat:last-child').length == 0) {
            $('.chats').append(
              `<div class="chat">
                    <div class="chat-avatar">
                        <span class="avatar box-shadow-1 cursor-pointer ` + (response.data.note.user_id == response.data.security_awareness.created_by ? 'bg-light-success' : 'bg-light-primary') + ` ">
                            <div class="avatar-content" data-bs-toggle="tooltip" data-bs-placement="top" title="${lang['user']} ${userName}" data-bs-original-title="${lang['user']}">${customUserName}</div>
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
              <span class="avatar box-shadow-1 cursor-pointer ` + (response.data.note.user_id == response.data.security_awareness.created_by ? 'bg-light-success' : 'bg-light-primary') + ` ">
                  <div class="avatar-content" data-bs-toggle="tooltip" data-bs-placement="top"
                      title="${lang['user']} ${userName}"
                      data-bs-original-title="${lang['user']}">${customUserName}</div>
              </span>
          </div>

          <div class="chat-body">
              <div class="chat-content">
                  <p class="download-security_awareness-note-file cursor-pointer"><u>${response.data.note.display_name}</u></p>
                  <p style="text-align: right"><small><b>${response.data.note.created_at}</b></small></p>
              </div>
          </div>
        </div>`
            );

          } else {
            $('.chat:last-child .chat-body').append(
              `<div class="chat-content">
                <p class="download-security_awareness-note-file cursor-pointer"><u>${response.data.note.display_name}</u></p>
                <p style="text-align: right"><small><b>${response.data.note.created_at}</b></small></p>
              </div>`
            );
          }
          $(attachSecurityAwarenessSelector).val('').trigger('change');
          $('.user-chats').scrollTop($('.user-chats > .chats').height());
        } else {
          // showError(data['errors']);
        }
      }
      , error: function (response, data) {
        const responseData = response.responseJSON;
        makeAlert('error', responseData.message, lang['error']);
        // showError(responseData.errors);
      }
    });
  }
}

// Handle change event for file to display file name
$('.attach-doc').on('change', function () {
  const fileNamecontent = $(this).parents('.chat-app-form').prev();
  try {
    fileNamecontent.text(fileNamecontent.data('content').replace('()', `(${$(this)[0].files[0].name})`));
  } catch (error) {
    fileNamecontent.text('');
  }

});

// Download note file start
$(document).on('click', '.download-security_awareness-note-file', function () {
  const form = $('#download-security_awareness-note-file-form');
  form.find('[name="id"').val($(this).data('id'));
  form.find('[name="security_awareness_id"').val($(this).data('securityAwarenessId'));
  form.trigger('submit');
});
// Download note file End

function addMessageToChat(securityAwareness) {
  const notes = securityAwareness.notes
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
                      <span class="avatar box-shadow-1 cursor-pointer ` + (note.user_id == securityAwareness.created_by ? 'bg-light-success' : 'bg-light-primary') + ` ">
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
            + (note.type == 't' ? `<p>${note.note}</p>` : `<p class="download-security_awareness-note-file cursor-pointer" data-id='${note.id}' data-security-awareness-id='${securityAwareness.id}'><u>${note.note}</u></p>`) +
            `<p style="text-align: right"><small><b>${note.created_at}</b></small></p>
              </div>`
          );
        } else {
          $('.chats').append(
            `<div class="chat chat-left user${note.user_id}">
                  <div class="chat-avatar">
                      <span class="avatar box-shadow-1 cursor-pointer ` + (note.user_id == securityAwareness.created_by ? 'bg-light-success' : 'bg-light-primary') + ` ">
                          <div class="avatar-content" data-bs-toggle="tooltip" data-bs-placement="top" title="${lang['user']}: ${note.user_name}" data-bs-original-title="${lang['user']}">${note.custom_user_name}</div>
                      </span>
                  </div>
                  <div class="chat-body">
                        <div class="chat-content">`
            + (note.type == 't' ? `<p>${note.note}</p>` : `<p class="download-security_awareness-note-file cursor-pointer" data-id='${note.id}' data-security-awareness-id='${securityAwareness.id}'><u>${note.note}</u></p>`) +
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
                    <span class="avatar box-shadow-1 cursor-pointer ` + (note.user_id == securityAwareness.created_by ? 'bg-light-success' : 'bg-light-primary') + ` ">
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
                    <span class="avatar box-shadow-1 cursor-pointer ` + (note.user_id == securityAwareness.created_by ? 'bg-light-success' : 'bg-light-primary') + ` ">
                        <div class="avatar-content" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="${lang['user']}: ${note.user_name}" data-bs-original-title="${lang['user']}">${note.custom_user_name}</div>
                    </span>
                </div>
                
                <div class="chat-body">
                    <div class="chat-content">`
            + (note.type == 't' ? `<p>${note.note}</p>` : `<p class="download-security_awareness-note-file cursor-pointer" data-id='${note.id}' data-security-awareness-id='${securityAwareness.id}'><u>${note.note}</u></p>`) +
            `<p style="text-align: right"><small><b>${note.created_at}</b></small></p>
                    </div>
                </div>
              </div>`
          );

        } else {
          $('.chat:last-child .chat-body').append(
            `<div class="chat-content">`
            + (note.type == 't' ? `<p>${note.note}</p>` : `<p class="download-security_awareness-note-file cursor-pointer" data-id='${note.id}' data-security-awareness-id='${securityAwareness.id}'><u>${note.note}</u></p>`) +
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
