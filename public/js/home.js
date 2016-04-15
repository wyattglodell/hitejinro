(function() {
	function setInstafeed(site, loadLimit) {
		var userFeedOptions,
			userFeed,
			ids = {
				'hite': {
					'userId': 264559156,
					'accessToken': '264559156.1677ed0.9698238690854cdbbe49b8772e7358ad'
				},
				'jinro': {
					'userId': 3109851832,
					'accessToken': '3109851832.1677ed0.ec1c4bb4e13447de982f271d99946296'
				},
			};

		userFeedOptions = {
	        get: 'user',
	        limit: loadLimit,
	        sortBy: 'most-recent',
	        userId: ids[site]['userId'],
	        accessToken: ids[site]['accessToken'],
	        template: '<a href="{{link}}" target="_blank"><img src="{{image}}" /></a>',
	        error: function(e) {
	        	setInstafeed('hite', 6);
	        }
	    };
	    if($(window).width() > 500) {
	    	userFeedOptions.resolution = 'low_resolution';
	    }
	    userFeed = new Instafeed(userFeedOptions);
	    userFeed.run();
	}
	
	$(function() {
		var site = $('#instafeed').data('site');

		if (site) setInstafeed(site, 6);
	});
})();