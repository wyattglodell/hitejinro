(function() {
	$(function() {
		$('#menu-toggle').on('click', function() {
			$('header').toggleClass('menu-open');
			$(this).toggleClass('open');
		});
	});
})();