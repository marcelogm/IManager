<?php
namespace IManager\Views\Ajax;
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">&times;</button>
	<h4>
		Renomear imagem
	</h4>
</div>
<div class="modal-body" id="feedback-modal-body">
	<form id="edit-image-form">
		<div class="form-group">
			<label for="edit-image-name">Nome</label>
			<div class="input-group">
				<input type="text" value="<?= pathinfo($image->name, PATHINFO_FILENAME) ?>" class="form-control" id="edit-image-name" name="image-name" placeholder="Nome da imagem." maxlength="45" required/ />
				<div class="input-group-addon">
					<?= '.' . $image->type ?>
				</div>
			</div>

		</div>
		<input type="submit" class="submit" style="display:none;" />
	</form>
</div>
<div class="modal-footer" id="feedback-modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
	<button type="button" class="btn btn-primary" id="btn-img-edit-confirm">Salvar alteração</button>
</div>