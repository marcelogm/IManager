<?php
namespace IManager\Views\Ajax;
?>

<?php if($message): ?>
<div class="container">
	<div class="alert alert-danger" role="alert">
		<?= $message ?>
	</div>
</div>
<?php endif; ?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">&times;</button>
	<h4>
		<?= $title; ?>
	</h4>
</div>
<div class="modal-body" id="feedback-modal-body">
	<p>
		<?= $body; ?>
	</p>
</div>


