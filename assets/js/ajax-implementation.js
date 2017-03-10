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

	$(document).on( 'click', '.navPage .nav-links a', function( event ) {
		event.preventDefault();

		page = find_page_number( $(this) );
        $('.paginateNumber').text(page);
        //alert(page);

		$.ajax({
			url: ajaximplementation.ajaxurl,
			type: 'post',
			data: {
				action: 'test_func',
				query_vars: ajaximplementation.query_vars,
				page: page
			},
			success: function( html ) {
                //console.log(html);
				$('.wrapFunc').remove();
				//$('.navPage').remove();
				$('.entry-content_asd').append( html );
                //$('.navPage').append( html );
			}
		})
	})
})(jQuery);