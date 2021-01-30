/*
@package: Zipp
@version: 0.1 <2019-06-13>
*/


// Dev Modules
AdminPages.listen( 'devmodules', async r => {

	const l = r.lang;

	// need to implement widget functionallity

	const cfgs = [];

	for ( let k in r.cfgs ) {
		const obj = r.cfgs[k];
		obj.name = k;
		cfgs.push( obj );
	}

	r.main = `
<h1>${ r.title }</h1>

<div class="basic-cont">
	<table class="dev-info-modules">
		<tr>
			<td>Stage</td>
			<td>Name</td>
			<td>Version</td>
			<td>Events</td>
			<td>Dependencies</td>
		</tr>
		${ cfgs.sort( (a, b) => a.stage - b.stage ).map( c => `<tr>
			<td>${ c.stage }</td>
			<td>${ c.name }</td>
			<td>${ c.version }</td>
			<td>${ c.events.join( ', ' ) }</td>
			<td>${ c.dependencies.join( ', ' ) }</td>
			</tr>` ).join('') }
	</table>
</div>`;

} );