(function() {
	function setInstafeed(loadLimit) {
		
		
		var userFeedOptions = {
	        get: 'user',
			userId: 264559156,
	        limit: loadLimit,
	        sortBy: 'most-recent',
			clientId: 'acf43080faa4455b84c62da2f57840b6',
	        accessToken: '264559156.acf4308.929ff8f3a2dc4cc4be71abb800082a4d',
	        template: '<li><a href="{{link}}" target="_blank"><span class="meta-bg"></span><span class="meta"><span class="likes"><span class="icon icon-heart"></span>{{likes}}</span><span class="comments"><span class="icon icon-comment"></span>{{comments}}</span><span class="caption">{{caption}}</span></span><img src="{{image}}" /></a></li>',
	        error: function(e) {
	        	//setInstafeed('hite', 6);
	        }
	    };
		
	    if($(window).width() > 500) {
	    	userFeedOptions.resolution = 'low_resolution';
	    }
		
	   var userFeed = new Instafeed(userFeedOptions);
	    userFeed.run();
	}
	
	$(function() {
		setInstafeed(8);
	});
})();