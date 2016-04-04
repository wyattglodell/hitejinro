<script>
	if ( !sessionStorage.getItem('passed') ) window.location.replace('/entry' + window.location.search);
</script>

<?php
	echo $tpl->header;
	echo "<main></main>";
	echo $tpl->footer;
?>

<script type="text/babel">
	class Home extends React.Component {
		constructor(props) {
			super(props);
			this.state = {
				currentSite: '<?php echo Site::get_current_site() ?>'
			}
		}

		render() {
			var site_data = JSON.parse('<?php echo json_encode($site_data) ?>');

			return (
				<div>
					<Featured items={site_data.theme.pages[this.state.currentSite]} currentSite={this.state.currentSite} />
				</div>
			);
		}
	}

	const Featured = (props) =>
		<div>
			{props.items
				.filter(item => item.featured)
				.sort((a, b) => a.featuredOrder < b.featuredOrder ? -1 : 1)
				.map((item, key) =>
					<a key={key} href={item.link}>
						<img src={'/public/img/' + props.currentSite + '/' + item.img}
							alt={item.headline} />
						<div>
							<h2>{item.headline}</h2>
							<p>{item.subtitle}</p>
						</div>
					</a>
				)}
		</div>

	ReactDOM.render(<Home />, document.querySelector('main'));
</script>