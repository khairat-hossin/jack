var data_mention_config = {};
var data_object_config = {};
var counter=2;
var visibility = '';
var staffid = '';

$(function () {
  "use strict";
  $.post(site_url + 'mention/mention_client/get_staff_mentions').done(function(response) {
      response = JSON.parse(response);

      $.fn.atwho.debug = true
    
    var names = response;

    var names = $.map(names,function(value,i) {
      return {'id':value.id,'name':value.name,'avatar':value.avatar};
    });

    data_mention_config = {
      at: "@",
      data: names,               
      headerTpl: '<div class="atwho-header">Member List<small>↑&nbsp;↓&nbsp;</small></div>',
      insertTpl: '<a href="#staff${id}" data-staff-id="data_staff_id_${id}">${name}</a>',
      displayTpl: "<li><img src='${avatar}'  height='20' width='20' /> ${name}</li>",
      limit: 200,
    }
    $('.inputor').atwho(data_mention_config);
  });

  $.post(site_url + 'mention/mention_client/get_object_mentions').done(function(response) {
      response = JSON.parse(response);

      $.fn.atwho.debug = true
    
    var object = response;

    var object = $.map(object,function(value,i) {
      return {'id':'value.id','name':value.name,'type':value.type,'link':value.link};
    });

    data_object_config = {
      at: "#",
      data: object,
      displayTpl: "<li>${name}</li>",
      insertTpl: '${link}',
      limit: 200,
    }
    $('.inputor').atwho(data_object_config);
    
  });

  $(window).scroll(function () {   
        if ($(document).height() -  $(window).height() == $(window).scrollTop()) {
            staffid = $('[name="staffid"]').val();
            appendData();
        }
  });
});
function post_inputor(){
  "use strict";
  var data = {};
  data.content = $('#inputor_post').html();
  $.post(site_url + 'mention/mention_client/add_post', data).done(function(response) {
        response = JSON.parse(response);
        if (response.success === true || response.success == 'true') {
            alert_float('success', response.message);
            window.location.reload();
        }else{
          alert_float('warning', response.message);
            window.location.reload();
        }
    });
};


/**
 * append data 
 * @return counter
 */
function appendData() {
    "use strict";
    $.post(site_url + 'mention/mention_client/load_mention_ajax/0/'+counter).done(function(response) {
        response = JSON.parse(response);
        $('#newsfeed_data_internal').append(response);
        $('.inputor').atwho(data_mention_config).atwho(data_object_config);
        
        $('.media-left a').hover(function() {
        var id = $(this).data('id');
        $(this).stop().animate({opacity:.4},200);
        $('[data-testid="popover-'+id+'"]').removeClass('hide'); 
        $('[data-testid="popover-'+id+'"]').addClass('show'); 
        $('[data-testid="popover-'+id+'"]').fadeIn();
      }, function() {
          var id = $(this).data('id');
          $(this).stop().animate({opacity:1},500);
          $('[data-testid="popover-'+id+'"]').removeClass('show'); 
          $('[data-testid="popover-'+id+'"]').addClass('hide'); 
      });
    });
    counter++;
}

/*show comment ins*/
function toggle_comment(id) {
  "use strict";
  $('.js-commnet-hidden_'+id).toggle();
}

function show_view_comment(id) {
  "use strict";
  $('.js-commnet-hidden_'+id).css('display', 'block');
}
/*end*/

// Add new comment to post
function add_comment_isn(id) {
  "use strict";
  $("body").append('<div class="dt-loader"></div>');
  var form_data = new FormData();
  form_data.append('csrf_token_name', $('form').find('input[name="csrf_token_name"]').val());

  var content = $('#new_comment_post_'+id).html();
  var url = '';
  $.post(site_url + 'mention/mention_client/add_comment', {
      content: content,
      postid: id,
      url: url,
  }).done(function(response) {
      response = JSON.parse(response);
      $("body").find('.dt-loader').remove();
      if (response.success === true || response.success == 'true') {
          $('#new_comment_post_'+id).html('');
          var message = response.comment;
          if ($("body").find('[data-comments-postid="' + id + '"] .post-comment').length > 0) {
            $("body").find('[data-comments-postid="' + id + '"] .post-comment').append(message);
            var count = $("body").find('[data-comments-postid="' + id + '"] .post-comment .comment').length;
            $("body").find('#count_comment_'+id).html(count);
          }else{
            refresh_post_comments_isn(id);
            var count = $("body").find('[data-comments-postid="' + id + '"] .post-comment .comment').length;
            $("body").find('#count_comment_'+id).html(count);
          }
      }
  });
}

// Add new comment to post
function add_comment_detail_isn(id) {
  "use strict";
  $("body").append('<div class="dt-loader"></div>');
  var form_data = new FormData();
  form_data.append('csrf_token_name', $('form').find('input[name="csrf_token_name"]').val());

  var content = $('#new_comment_detail_post_'+id).html();
  var url = '';
  $.post(site_url + 'mention/mention_client/add_comment', {
      content: content,
      postid: id,
      url: url,
  }).done(function(response) {
      response = JSON.parse(response);
      $("body").find('.dt-loader').remove();
      if (response.success === true || response.success == 'true') {
          $('#new_comment_detail_post_'+id).html('');
          var message = response.comment;
          if ($("body").find('[data-comments-detail-postid="' + id + '"] .post-comment').length > 0) {
            $("body").find('[data-comments-detail-postid="' + id + '"] .post-comment').append(message);
            console.log($("body").find('[data-comments-detail-postid="' + id + '"] .post-comment .comment').html());
            var count = $("body").find('[data-comments-detail-postid="' + id + '"] .post-comment .comment').length;
            $("body").find('#count_comment_detail_'+id).html(count);
          }else{
            refresh_post_comments_isn(id);
            var count = $("body").find('[data-comments-detail-postid="' + id + '"] .post-comment .comment').length;
            $("body").find('#count_comment_detail_'+id).html(count);
          }
      }
  });
}

// Removes post comment
function remove_post_comment_isn(id, postid) {
  "use strict";
  $.post(site_url + 'mention/mention_client/remove_post_comment/' + id + '/' + postid).done(function(response) {
      response = JSON.parse(response);
      if (response.success == true || response.success == 'true') {
          $('.comment[data-commentid="' + id + '"]').remove();
          var count = $("body").find('[data-comments-postid="' + postid + '"] .post-comment .comment').length;
          $("body").find('#count_comment_'+postid).html(count);
          var count = $("body").find('[data-comments-detail-postid="' + postid + '"] .post-comment .comment').length;
          $("body").find('#count_comment_detail_'+postid).html(count);
      }
  });
}

// Delete post from database
function delete_post_isn(postid) {
  "use strict";
  if (confirm_delete()) {
      $.post(site_url + 'mention/mention_client/delete_post/' + postid, function(response) {
          if (response.success === true || response.success == 'true') { $('[data-main-postid="' + postid + '"]').remove(); }
      }, 'json');
  }
}

// Refreshing only post comments
function refresh_post_comments_isn(postid) {
  "use strict";
  $.post(site_url + 'mention/mention_client/init_post_comments/' + postid + '?refresh_post_comments=true').done(function(response) {
      $('[data-comments-postid="' + postid + '"]').append(response);
  });
}

