!function($){$(function(){var e=envira_editor_frame=!1,a=function(a){a.preventDefault(),$(".envira-gallery-default-ui .selected").removeClass("details selected"),$(".envira-gallery-default-ui").appendTo(".envira-gallery-default-ui-wrapper").hide(),e=envira_editor_frame=!1};$(document).on("click",".envira-gallery-choose-gallery, .envira-gallery-modal-trigger",function(l){l.preventDefault(),e=l.target,envira_editor_frame=!0,$(".envira-gallery-default-ui").appendTo("body").show(),$(document).on("click",".media-modal-close, .media-modal-backdrop, .envira-gallery-cancel-insertion",a),$(document).on("keydown",function(e){27==e.keyCode&&envira_editor_frame&&a(e)})}),$(document).on("click",".envira-gallery-default-ui .thumbnail, .envira-gallery-default-ui .check, .envira-gallery-default-ui .media-modal-icon",function(e){e.preventDefault(),$(this).parent().parent().hasClass("selected")?($(this).parent().parent().removeClass("details selected"),$(".envira-gallery-insert-gallery").attr("disabled","disabled")):($(this).parent().parent().parent().find(".selected").removeClass("details selected"),$(this).parent().parent().addClass("details selected"),$(".envira-gallery-insert-gallery").removeAttr("disabled"))}),$(document).on("click",".envira-gallery-default-ui .check",function(e){e.preventDefault(),$(this).parent().parent().removeClass("details selected"),$(".envira-gallery-insert-gallery").attr("disabled","disabled")}),$(document).on("click",".envira-gallery-default-ui .envira-gallery-insert-gallery",function(l){if(l.preventDefault(),$(e).hasClass("envira-gallery-choose-gallery"))wp.media.editor.insert('[envira-gallery id="'+$(".envira-gallery-default-ui .selected").data("envira-gallery-id")+'"]');else{var r={action:"envira_gallery_load_gallery_data",post_id:$(".envira-gallery-default-ui:first .selected").data("envira-gallery-id")};$.post(ajaxurl,r,function(e){$(document).trigger({type:"enviraGalleryModalData",gallery:e}),a(l)},"json")}a(l)})})}(jQuery);