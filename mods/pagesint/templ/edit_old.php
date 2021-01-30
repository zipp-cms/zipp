<form class="edit-cont" method="POST" data-ajax="editpage">

	<?= $p->nonce() ?>
	<?= $mainF->pageId->render( $page ) ?>
	<?= $mainF->ctnId->render( $page ) ?>
	<?= $mainF->lang->render( $page ) ?>
	<?= $mainF->layout->render( $page ) ?>

	<div class="main-edit">

		<div class="edit-infos">

			<div class="left">
				<?= $mainF->title->render( $page ) ?>
			</div>
			<div class="right">
				<?= $mainF->url->render( $page ) ?>
			</div>

		</div>

		<?php foreach ( $comps as $c ) { ?>

		<div class="page-component">
			<h2><?= $c->name ?></h2>
			<?php if ( !isNil( $c->desc ) ): ?>
			<p><?= $c->desc ?></p>
			<?php endif; ?>
			<div class="fields-grid">
				<?= $c->fields ?>
			</div>
		</div>

		<?php } ?>

		<div class="page-component">
			<h2>Options</h2>
			<div class="fields-grid">
				<?= $mainF->keywords->render( $page ) ?>
			</div>
		</div>

		

	</div>

	<div class="state-edit">

		<?php if ( cLen( $langDrop ) ): ?>
		<div class="page-lang-cont">
			<label><?= $l->pageLang ?>:</label>
			<?= $langDrop ?>
		</div>
		<?php endif; ?>

		<?php if ( cLen( $newLangDrop ) ): ?>
		<div class="new-page-lang-cont">
			<button id="create-new-lang"><?= $l->createNewLang ?></button>
		</div>
		<?php endif; ?>

		<a href="<?= $previewLink ?>" target="_blank"><?= $l->previewLink ?></a>

		<div class="page-info">
			<p>Created: <?= $t->time( $page->createdOn, 'Y-m-d H:i:s', $page->lang ) ?></p>
			
			<?= $mainF->state->render( $page ) ?>
			<?= $mainF->publishOn->render( $page ) ?>

			<p>Saved: <span id="saved">false</span></p>
		</div>

		<div class="form-msgs"></div>
		<input type="submit" name="edit-page" value="<?= $l->pageSave ?>">

		<a href="" id="del-page" data-ajax="delpage" data-msg=".state-edit .form-msgs" data-quest="<?= $l->delQuest ?>" data-data="<?= e( $delData ) ?>"><?= $l->pageDel ?></a>

	</div>

</form>

<div class="new-page-pop">

	<div class="close"><?= $l->close ?></div>

	<form method="POST" data-ajax="newpagelang">

		<?= $newPageLangNonce ?>
		<input type="hidden" name="pageid" value="<?= $page->pageId ?>">

		<label><?= $l->newLang ?>:</label>
		<?= $newLangDrop ?>

		<div class="form-msgs"></div>
		<input type="submit" name="new-page-lang" value="<?= $l->createNewLang ?>">

	</form>

</div>