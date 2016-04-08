(function() {
	var AgeServices = {
		pad: function(v, l) {
			while (v.length < l)
				v = '0' + v;
			return v;
		},

		isValid: function(m, d, y) {
			if (y < 1000 || y > 3000 || m == 0 || m > 12)
	    		return false;

	    	var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];
	    	if(y % 400 == 0 || (y % 100 != 0 && y % 4 == 0))
	   			monthLength[1] = 29;

	   		if (d == 0 || d > monthLength[m - 1])
	   			return false;

	   		return true;
		},

		getFromInput: function(date) {
			var today = new Date(),
				age = today.getFullYear() - date.getFullYear(),
				m = today.getMonth() - date.getMonth();

			if (m < 0 || (m === 0 && today.getDate() < date.getDate()))
				age--;

			return age;
		}
	};

	var verifyWithInput = function($target) {
		var m = parseInt($target.parent().find('#verify-month').val(), 10),
			d = parseInt($target.parent().find('#verify-day').val(), 10),
			y = parseInt($target.parent().find('#verify-year').val(), 10);

		if (!m || !d || !y) {
			set_msg('Please tell us your age.');
			return false;
		}

		if (!AgeServices.isValid(m, d, y)) {
			set_msg('Please input a valid age.');
			return false;
		}

		return AgeServices.getFromInput(new Date(y, m-1, d));
	};

	var verifyWithFB = function($target) {
		var checkAgeRange = function() {
			FB.api(
				'/me?fields=age_range',
				function(response) {
					if (response && !response.error) {
						$target.siblings('[name="age"]').val(response.age_range.min)
							   .parent('form').submit();
					}
				});
		};

		FB.getLoginStatus(function(response) {
			if (response.status === 'connected')
				checkAgeRange();
			else
				FB.login(checkAgeRange);
		});
	};
	
	$(function() {
		//on page load focus on month field
		$('#verify-month').focus();

		$('input[maxlength="2"]')
		//on input, if reaches length auto tab to next field
			.on('input', function(e) {
				var target = e.target;
				if (target.value && target.value.length == 2)
					$(target).next().focus();
			})
		//on blur, format input value
			.on('blur', function(e) {
				var target = e.target;
				if (target.value)
					target.value = AgeServices.pad(target.value, 2);
			});

		//on form submit, validate
		$('#verify-with-input').on('click', function(e) {
			var $target = $(e.target),
				$field_age = $target.siblings('[name="age"]'),
				age;

			if ( !$field_age.val() ) {
				e.preventDefault();
				age = verifyWithInput($target);

				if (age || age === 0) {
					$field_age.val(age);
					$target.parent('form').submit();
				}
			}
		});

		$('#verify-with-fb').on('click', function(e) {
			e.preventDefault();
			verifyWithFB($(e.target));
		});
	});	
})();