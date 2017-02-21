<?php
namespace IManager\Views\Ajax;
use IManager\Utils\Helpers;
?>

<?php if($search): ?>
<div class="container">
	<div class="alert alert-info" role="alert">
		<p>
			Pesquisando por "
			<span class="current-search">
				<?= $search ?>
			</span>".
			<?php if($count != 0): ?>
			<br />
			<small>
				Pesquisa com o total de
				<span class="current-search">
					<?= $count ?>
				</span>resultado(s).
			</small>
			<?php endif; ?>
		</p>
	</div>
</div>
<?php endif; ?>

<?php if($message): ?>
<div class="container">
	<div class="alert alert-danger" role="alert">
		<?= $message ?>
	</div>
</div>
<?php endif; ?>

<?php foreach($images as $image): ?>
<div class="col-sm-6 col-md-2 image" id="<?= $image->id ?>">
	<a href="#" class="thumbnail">
		<?= Helpers::img($image->thumb, ['class' => 'image-item']) ?>
	</a>
	<div class="image-info-<?= $image->id ?>">
		<input id="path" value="<?= $image->url ?>" hidden />
		<input id="thumb" value="<?= $image->thumb ?>" hidden />
		<input id="type" value="<?= strtoupper($image->type) ?>" hidden />
		<input id="hash" value="<?= $image->hash ?>" hidden />
		<input id="size" value="<?= Helpers::format_bytes($image->size) ?>" hidden />
		<input id="name" value="<?= $image->name ?>" hidden />
		<input id="width" value="<?= $image->width ?>" hidden />
		<input id="height" value="<?= $image->height ?>" hidden />
		<input id="created" value="<?= $image->created ?>" hidden />
	</div>
</div>
<?php endforeach; ?>

<?php if($more): ?>
<div class="container col-md-12 more">
	<a class="btn-img-more" id="<?= $next ?>">
		<h3 class="text-center">
			<span class="glyphicon glyphicon-picture" aria-hidden="true"></span>Carregar mais imagens
		</h3>
	</a>
</div>
<?php endif; ?>