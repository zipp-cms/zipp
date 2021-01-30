<h1><?= $title ?></h1>
<?= $langDrop ?>
<form class="site-edit" method="POST" data-ajax="<?= $key ?>">
	<?= $p->nonce() ?>
	<input type="hidden" name="key" value="<?= $key ?>">
	<input type="hidden" name="baselang" value="<?= $lang ?>">
	<div class="fields-grid">
		<?= $fields ?>
	</div>
	<div class="form-msgs"></div>
	<input type="submit" name="site-edit" value="<?= $l->siteSave ?>">
</form>