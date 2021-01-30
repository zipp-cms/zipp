<h1><?= $l->newPageTitle ?></h1>

<form class="new-cont" method="POST" data-ajax="newpage">

	<?= $p->nonce() ?>

	<div class="fields-grid">

		<?= $fields ?>

	</div>

	<div class="form-msgs"></div>

	<input type="submit" name="newpage" value="<?= $l->newPage ?>">

</form>