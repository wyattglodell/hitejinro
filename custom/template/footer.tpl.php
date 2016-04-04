<footer class="main"></footer>

<script type="text/babel">
	class Footer extends React.Component {
		constructor(props) {
			super(props);
		}

		render() {
			var site_data = JSON.parse('<?php echo json_encode($site_data) ?>'),
				year = new Date().getFullYear();

			return (
				<div>
					<Newsletter />
					<Navigation items={site_data.theme.footer_links} />
					<Social items={site_data.theme.social} />
					<small>&copy; {year} HiteJinro.  All rights reserved.</small>
				</div>
			);
		}
	}

	class Newsletter extends React.Component {
		render() {
			return (
				<div>
					<h3>Get our newsletter</h3>
					<input ref='email' type='email' placeholder='e-mail address' />
					<button type='submit'>Sign up</button>
				</div>
			)
		}
	}

	const Navigation = (props) => 
		<ul>
			{Object.keys(props.items).map((key) =>
				<li key={props.items[key]}><a href={props.items[key]}>{key}</a></li>
			)}
		</ul>
		
	const Social = (props) => 
		<ul>
			{Object.keys(props.items).map((key) =>
				<li key={key}><a href={props.items[key]}><span className={'icon icon-' + key}></span>{key[0]}</a></li>
			)}
		</ul>

	ReactDOM.render(<Footer />, document.querySelector('footer.main'));
</script>