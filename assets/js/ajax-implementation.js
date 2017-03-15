(function($) {

	function find_page_number( element ) {
		var pageindex = $('.paginateNumber').html();
        if (element.hasClass( "next" )){
            var pagesum = parseInt(pageindex) + 1;
            return pagesum;
        } else {
            var pagesum = parseInt(pageindex) - 1;
            return pagesum;
        }
	}

	$(document).on( 'click', '.slidePostsNav .nav-links a', function( event ) {
		event.preventDefault();

		page = find_page_number( $(this) );
        $('.paginateNumber').text(page);

		$.ajax({
			url: ajaximplementation.ajaxurl,
			type: 'post',
			data: {
				action: 'slidepost_ajax_pagination',
				page: page
			},
            beforeSend: function() {
                $('.slidePostsContainer').fadeOut();
        		$('html,body').animate({scrollTop: 200}, 300);
        		$('.wrapSlidePosts .slidePostsTab').append( '<div class="wrapLoader" id="spLoader">Loading New Posts...</div>' );
        	},
			success: function( html ) {
                $('.wrapSlidePosts #spLoader').remove();
				$('.slidePostsContainer').remove();
				$('.wrapSlidePosts').append( html );
                $('.slidePostsNav .nav-links a').removeAttr("href"); /* remove href attribute inside links - add button role to anchor elements in the pagination */
                $('.slidePostsNav .nav-links a').attr('role', 'button'); /* add button role to anchor elements in the pagination */
			}
		})
	})
})(jQuery);