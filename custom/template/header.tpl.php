<header class="main"></header>

<script type="text/babel">
	class Header extends React.Component {
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
					<SitesToggle
						sites={['hite', 'jinro']}
						currentSite={this.state.currentSite} />
					<Navigation items={site_data.theme.pages[this.state.currentSite]} />
					<h1>
						<a href={'?site=' + this.state.currentSite}>
							<img src={'/public/img/logo_' + this.state.currentSite + '.png'}
							alt={this.state.currentSite + ' logo'} />
						</a>
					</h1>
					<Social items={site_data.theme.social} />
				</div>
			);
		}
	}

	const SitesToggle = (props) => 
		<div>
			{props.sites.map((site, i) => {
				return <Site key={i} site={site} currentSite={props.currentSite} />;
			})}
		</div>

	const Site = (props) => 
		<a href={'?site=' + props.site}
			className={props.currentSite === props.site ? 'active' : null}>
			<span>
				<img
					src={'/public/img/logo_sm_' + props.site + '.png'}
					alt={'switch to' + props.site} />
			</span>
		</a>

	const Navigation = (props) => 
		<ul>
			{props.items.map((item, key) =>
				<li key={key}><a href={item.link}>{item.headline}</a></li>
			)}
		</ul>
		
	const Social = (props) => 
		<ul>
			{Object.keys(props.items).map((key) =>
				<li key={key}><a href={props.items[key]}><span className={'icon icon-' + key}></span>{key[0]}</a></li>
			)}
		</ul>

	ReactDOM.render(<Header />, document.querySelector('header.main'));
</script>