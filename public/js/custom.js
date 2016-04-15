(function() {
	$(function() {
		$('#menu-toggle').on('click', function() {
			$('body').toggleClass('menu-open');
			$(this).toggleClass('open');
		});
	});
})();