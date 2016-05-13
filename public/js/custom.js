$(function() {
	$('#menu-toggle').on('click', function() {
		$('body').toggleClass('menu-open');
		$(this).toggleClass('open');
	});
	
	
	
	$('body.fan-page .toggle').click(function() {
		if ($(this).hasClass('flipped')) {
			$('.grid-item:eq(5),.grid-item:eq(6),.grid-item:eq(7)').toggleClass('hide');
			$('.grid-item.text').toggleClass('flipped');
		} else {
			$('.grid-item:eq(5),.grid-item:eq(6),.grid-item:eq(7)').toggleClass('hide');
			
			$('.grid-item.text').toggleClass('flipped');
		}
	});
	
	if ($('#map-container').length) {
		$('#map-container').storeLocator({
			dataLocation: '/public/inc/store-addresses.json',
			fullMapStart: true,
			dataType: 'json',
			autoGeocode: false,
			infowindowTemplatePath: '//d3jepvayto7z0y.cloudfront.net/store-locator-infowindow2.html',
			listTemplatePath: '//d3jepvayto7z0y.cloudfront.net/store-locator-list2.html'
		});	
	}
});
