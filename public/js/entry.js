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
	},

	isInRange: function(age) {
		return age >= 21;
	}
};

var Verify = React.createClass({
	getInitialState: function() {
		return {
			error: false,
			errorMessage: '',
			denied: false
		}
	},

	componentDidMount: function() {
		ReactDOM.findDOMNode(this.refs.month).focus();
	},

	_formatInput: function(e) {
		var target = e.target;
		if (target.value)
			target.value = AgeServices.pad(target.value, 2);
	},

	_onError: function(message, denied=false) {
		this.setState({
			error: true,
			errorMessage: message,
			denied: denied
		});
	},

	_clearError: function() {
		this.setState({
			error: false,
			errorMessage: '',
			denied: false
		});			
	},

	_verifyAge: function(age, context) {
		if (!AgeServices.isInRange(age)) {
			this._onError('', true);
			return false;
		}

		this._clearError();
		this.props.grantPass();
	},

	_verifyWithInput: function() {
		var m = parseInt(this.refs.month.value, 10),
			d = parseInt(this.refs.day.value, 10),
			y = parseInt(this.refs.year.value, 10);

		if (!m || !d || !y) {
			this._onError('Please tell us your age.');
			return false;
		}

		if (!AgeServices.isValid(m, d, y)) {
			this._onError('Please input a valid age.');
			return false;
		}

		var age = AgeServices.getFromInput(new Date(y, m-1, d));
		this._verifyAge(age);
	},

	_handleInputChange: function(e) {
		var target = e.target;
		if (target.value && target.value.length == 2)
			ReactDOM.findDOMNode(target).nextElementSibling.focus();

	},

	_handleKeyPress: function(e) {
		if (e.charCode == 13)
			this._verifyWithInput();
	},

	_verifyWithFB: function() {
		var checkAgeRange = function() {
			var self = this;

			FB.api(
				'/me?fields=age_range',
				function(response) {
					if (response && !response.error) {
						self._verifyAge(response.age_range.min);
					}
				});
		}.bind(this);

		FB.getLoginStatus(function(response) {
			if (response.status === 'connected') {
				checkAgeRange();
				}
				else {
				FB.login(checkAgeRange);
				}
		});
	},

	render: function() {
		var headline = this.state.denied ? 'Sorry, you must be of legal drinking age to access this site.' : 'We need to check your ID';

		return (
			<div>
				<h1><img src='/public/img/logo_white.png' alt='logo' /></h1>
				<h2 >{headline}</h2>
				<p className={!this.state.denied && this.state.error ? 'show' : null}>{this.state.errorMessage}</p>
				<div className={!this.state.denied ? null : 'hide'}>
					<div>
						<input ref='month' type='number' maxLength='2' min='1' max='12' placeholder='MM' onBlur={this._formatInput} onChange={this._handleInputChange} />
						<input ref='day' type='number' maxLength='2' min='1' max='31' placeholder='DD' onBlur={this._formatInput} onChange={this._handleInputChange} />
						<input ref='year' type='number' maxLength='4' min='1' placeholder='YYYY' onKeyPress={this._handleKeyPress} />
						<button type='submit' onClick={this._verifyWithInput}>Enter</button>
					</div>
					<p>or</p>
					<button type='submit' onClick={this._verifyWithFB}>Verify with Facebook</button>
				</div>
				<div>
					<a href='/privacy-policy'>Privacy Policy</a>
					<a href='/terms-of-use'>Terms of Use</a>
				</div>
			</div>
		);
	}
});

var HiteOrJinro = React.createClass({
	_goToPage: function(site) {
		window.location.replace('?site=' + site);
	},

	render: function() {
		if (window.location.search) {
			this._goToPage(window.location.search.substring(6));
			return false;
		} else {
			return (
				<div>
					<div>
						<a onClick={this._goToPage.bind(null, 'hite')}><img src='/public/img/split_hite.png' /></a>
					</div>
					<div>
						<a onClick={this._goToPage.bind(null, 'jinro')}><img src='/public/img/split_jinro.png' /></a>
					</div>
					<div>
						<img src='/public/img/logo_color.png' />
					</div>
				</div>
			);
			
		}
	}
});

var Entry = React.createClass({
	getInitialState: function() {
		return {
			passed: sessionStorage.getItem('passed') || false
		}
	},

	_grantPass: function() {
		this.setState({
			passed: true
		});

		sessionStorage.setItem('passed', true);
	},

	render: function() {
		return this.state.passed ? <HiteOrJinro /> : <Verify grantPass={this._grantPass} />;
	}
});

ReactDOM.render(<Entry />, document.getElementById('body-inner'));