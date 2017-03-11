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
				query_vars: ajaximplementation.query_vars,
				page: page
			},
			success: function( html ) {
				$('.slidePostsContainer').remove();
				$('.wrapSlidePosts').append( html );
			}
		})
	})
})(jQuery);