/*=========================================================================================
    File Name: app-chat.js
    Description: Chat app js
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

'use strict';


// Add message to chat - function call on form submit
function enterChat(editFormSelector) {
  const url = URLs['sendObjectiveComment'];
  var formData = new FormData(document.querySelector(editFormSelector + ' form'));
  const editForm = $(editFormSelector);
  const message = editForm.find('.message').val();
  const attachDocumentSelector = '#attach-doc';
  if (/\S/.test(message) || $(attachDocumentSelector).length && $(attachDocumentSelector).val()) {
    $.ajax({
      url: url,
      type: "POST",
      contentType: false,
      processData: false,
      data: formData,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
      , success: function (response) {
        if (response.status) {
          makeAlert('success', response.message, lang['success']);
          // if first chat comment to be added
          if ($('.chat:last-child').length == 0) {
            $('.chats').append(
              `<div class="chat">
                    <div class="chat-avatar">
                        <span class="avatar box-shadow-1 cursor-pointer bg-light-primary">
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
              <span class="avatar box-shadow-1 cursor-pointer bg-light-primary">
                  <div class="avatar-content" data-bs-toggle="tooltip" data-bs-placement="top"
                      title="${lang['user']} ${userName}"
                      data-bs-original-title="${lang['user']}">${customUserName}</div>
              </span>
          </div>

          <div class="chat-body">
              <div class="chat-content">
                 ${message ? ` <p>${message}</p>` : ''} 
                 ${response.data.comment.file_display_name ?
                `<p class="cursor-pointer download-comment-file" data-comment-id="${response.data.comment.id}"><u>${response.data.comment.file_display_name}</u></p>` :
                ''}
                  <p style="text-align: right"><small><b>${response.data.comment.formatted_created_at}</b></small></p>
              </div>
          </div>
        </div>`
            );

          } else {
            $('.chat:last-child .chat-body').append(
              `<div class="chat-content">
              ${message ? ` <p>${message}</p>` : ''} 
              ${response.data.comment.file_display_name ?
                `<p class="cursor-pointer download-comment-file" data-comment-id="${response.data.comment.id}"><u>${response.data.comment.file_display_name}</u></p>` :
                ''}
            <p style="text-align: right"><small><b>${response.data.comment.formatted_created_at}</b></small></p>
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
}

function downloadCommentFile(comment_id) {
  const url = URLs['downloadObjectiveCommentFile'] + "/" + comment_id;
  var link = document.createElement("a");
  link.href = url;
  link.style.display = "none";
  document.body.appendChild(link);

  link.click();

  // Cleanup
  document.body.removeChild(link);
}

$('.chats').on('click', '.download-comment-file', function () {
  const commentId = $(this).data('comment-id');
  downloadCommentFile(commentId);
});

// Handle change event for file to display file name
$('.attach-doc').on('change', function () {
  const fileNamecontent = $(this).parents('.chat-app-form').prev();
  try {
    fileNamecontent.text(fileNamecontent.data('content').replace('()', `(${$(this)[0].files[0].name})`));
  } catch (error) {
    fileNamecontent.text('');
  }

});

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


function addMessageToChat(comments) {
  // Reset chat content
  $('.chats').html('');
  comments.forEach(comment => {
    // comment from other user
    if (user_id != comment.user_id) {
      // if first chat comment to be added
      if ($('.chat:last-child').length == 0) {
        $('.chats').append(
          `<div class="chat chat-left user${comment.user_id}">
                  <div class="chat-avatar">
                      <span class="avatar box-shadow-1 cursor-pointer">
                          <div class="avatar-content" data-bs-toggle="tooltip" data-bs-placement="top" title="${lang['user']}: ${comment.user_name}" data-bs-original-title="${lang['user']}">${comment.custom_user_name}</div>
                      </span>
                  </div>
                  <div class="chat-body">
                  </div>
                </div>`
        );
      }
      if ($('.chat:last-child').hasClass('chat-left') && $('.chat:last-child').hasClass(`user${comment.user_id}`)) {
        $('.chat:last-child .chat-body').append(
          `<div class="chat-content">
  ${comment.comment ? ` <p>${comment.comment}</p>` : ''} 
                 ${comment.file_display_name ?
            `<p class="cursor-pointer download-comment-file" data-comment-id="${comment.id}"><u>${comment.file_display_name}</u></p>` :
            ''}
                  <p style="text-align: right"><small><b>${comment.created_at}</b></small></p>
              </div>`
        );
      } else {
        $('.chats').append(
          `<div class="chat chat-left user${comment.user_id}">
                  <div class="chat-avatar">
                      <span class="avatar box-shadow-1 cursor-pointer">
                          <div class="avatar-content" data-bs-toggle="tooltip" data-bs-placement="top" title="${lang['user']}: ${comment.user_name}" data-bs-original-title="${lang['user']}">${comment.custom_user_name}</div>
                      </span>
                  </div>
                  <div class="chat-body">
                        <div class="chat-content">
  ${comment.comment ? ` <p>${comment.comment}</p>` : ''} 
                 ${comment.file_display_name ?
            `<p class="cursor-pointer download-comment-file" data-comment-id="${comment.id}"><u>${comment.file_display_name}</u></p>` :
            ''}
                  <p style="text-align: right"><small><b>${comment.created_at}</b></small></p>
                        </div>
                    </div>
                </div>`
        );
      }
    } else {
      // Comment from me
      // if first chat comment to be added
      if ($('.chat:last-child').length == 0) {
        $('.chats').append(
          `<div class="chat">
                <div class="chat-avatar">
                    <span class="avatar box-shadow-1 cursor-pointer">
                        <div class="avatar-content" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="${lang['user']}: ${comment.user_name}" data-bs-original-title="${lang['user']}">${comment.custom_user_name}</div>
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
                    <span class="avatar box-shadow-1 cursor-pointer">
                        <div class="avatar-content" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="${lang['user']}: ${comment.user_name}" data-bs-original-title="${lang['user']}">${comment.custom_user_name}</div>
                    </span>
                </div>
                
                <div class="chat-body">
                    <div class="chat-content">
  ${comment.comment ? ` <p>${comment.comment}</p>` : ''} 
                 ${comment.file_display_name ?
            `<p class="cursor-pointer download-comment-file" data-comment-id="${comment.id}"><u>${comment.file_display_name}</u></p>` :
            ''}
                  <p style="text-align: right"><small><b>${comment.created_at}</b></small></p>
                    </div>
                </div>
              </div>`
        );

      } else {
        $('.chat:last-child .chat-body').append(
          `<div class="chat-content">
  ${comment.comment ? ` <p>${comment.comment}</p>` : ''} 
                 ${comment.file_display_name ?
            `<p class="cursor-pointer download-comment-file" data-comment-id="${comment.id}"><u>${comment.file_display_name}</u></p>` :
            ''}
                  <p style="text-align: right"><small><b>${comment.created_at}</b></small></p>
              </div>`
        );
      }
    }
    $('.message').val('');
    $('.user-chats').scrollTop($('.user-chats > .chats').height());
  });
}
