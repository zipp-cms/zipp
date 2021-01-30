<!DOCTYPE html>
<html lang="<?= $l->getActiveLang() ?>">
<head>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Zipp <?= $l->admin ?> - <?= $l->loading ?></title>

	<?php foreach ( $a->styles as $url ) { ?>
	<link rel="stylesheet" href="<?= $url ?>">
	<?php } ?>

	<script>
		const globalAjaxUrl = '<?= $ajax->baseUrl() // maybe should encode this ?>';
		// maybe will need to change this
		const coreUrl = url => `<?= $r->url() ?>${ url }`;
		const adminUrl = url => `<?= $a->bUrl() ?>${ url }/`;
		const globalBasePath = '<?= $r->basePath ?>';
		const buildTitle = title => `Zipp <?= $l->admin ?> - ${ title }`;
	</script>

</head>
<body class="loading">

	<noscript><?= $l->noScript ?></noscript>

	<div class="loader"></div>

	<div class="main-container<?= $showNav ? ' show-nav' : '' ?>">

		<div class="logo-cont">
			<div class="logo-ph"></div>
		</div>

		<nav class="top-nav">
			<div class="left-nav-ph"></div>
			<div class="right-nav-ph"></div>
		</nav>

		<nav class="sec-nav">
			<div class="sec-item-ph"></div>
			<div class="sec-item-ph"></div>
			<div class="sec-item-ph"></div>
		</nav>

		<main>