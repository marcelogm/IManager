<?php
namespace IManager\Views\Ajax;
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">&times;</button>
	<h4>
		Exclusão de imagem
	</h4>
</div>
<div class="modal-body" id="feedback-modal-body">
	<div>
		Você tem certeza que deseja excluir a imagem "<?= $image->name ?>"?
	</div>
</div>
<div class="modal-footer" id="feedback-modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
	<button type="button" class="btn btn-danger" id="btn-img-delete-confirm">Excluir imagem</button>
</div>