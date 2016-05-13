(function() {
	function setInstafeed(site, hash, loadLimit) {
		var limit = 6;
		var userFeedOptions,
			userFeed, i;

		userFeedOptions = {
	        get: 'user',
			userId: 264559156,
	        limit: 500,
			tagName: hash,
	        sortBy: 'most-recent',
			clientId: 'acf43080faa4455b84c62da2f57840b6',
	        accessToken: '264559156.acf4308.929ff8f3a2dc4cc4be71abb800082a4d',
	        template: '<a href="{{link}}" target="_blank"><img src="{{image}}" /></a>',
	        error: function(e) {
	        	//setInstafeed('hite', 6);
	        },
			filter: function(image) {	
				if (limit <= 0) return false;
							
				for (i in hash)
				{
					if (image.tags.indexOf(hash[i]) >= 0) { limit--; return true; }
				}
				
				return false;
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
		var hash = $('#instafeed').data('hash').split(',');

		if (site) setInstafeed(site, hash, 6);
	});
})();