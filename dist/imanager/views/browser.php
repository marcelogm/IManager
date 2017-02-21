<?php
namespace IManager\Views;
use IManager\Utils\Helpers;
?>

<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?= Helpers::link_favicon('favicon.ico'); ?>
	<title>IManager </title>

	<?= Helpers::link_css('bootstrap.min.css'); ?>
	<?= Helpers::link_css('bootstrap.fd.css'); ?>
	<?= Helpers::link_css('imanager.css'); ?>
</head>
<body>
	<div>
		<input id="implugin-secret-key" name="implugin-secret-key" value="<?= $secret ?>" hidden />
		<input id="implugin-base-path" value="<?= IMPLUGIN_BASE_URL ?>" hidden />
		<input id="implugin-offset" value="0" hidden />
		<input id="implugin-selected" value="0" hidden />
	</div>
	<div class="loading">
		<div class="sk-cube-grid">
			<div class="sk-cube sk-cube1"></div>
			<div class="sk-cube sk-cube2"></div>
			<div class="sk-cube sk-cube3"></div>
			<div class="sk-cube sk-cube4"></div>
			<div class="sk-cube sk-cube5"></div>
			<div class="sk-cube sk-cube6"></div>
			<div class="sk-cube sk-cube7"></div>
			<div class="sk-cube sk-cube8"></div>
			<div class="sk-cube sk-cube9"></div>
		</div>
	</div>
	<div class="modal fade" id="feedback-modal" tabindex="-1" role="dialog" aria-labelledby="feedback-modal-label">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content" id="feedback-modal-content">
				...
			</div>
		</div>
	</div>
	<div class="container-fluid">
		<nav class="navbar navbar-default navbar-fixed-top">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#">IManager</a>
				</div>
				<div class="navbar-form navbar-left search-form">
					<div class="input-group">
						<input type="text" class="form-control" id="implugin-search" name="implugin-search" placeholder="Nome do arquivo" />
						<div class="input-group-addon" id="btn-img-search">
							<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
							Pesquisar
						</div>
					</div>
				</div>
				<div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right">
						<li class="active">
							<a href="#" id="btn-img-upload">
								<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
								Adicionar
							</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>
	</div>
	<div class="row container-fluid image-panel"></div>
	<footer class="image-info-footer" id="footer" hidden>
		<div class="container-fluid image-info-display">
			<div class="col-md-2 visible-lg image-wrapper">
				<img src="#" class="image-info-gallery" alt="Imagem de demonstração" />
				<div class="overlay">
					<h4>
						<span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
						Clique para ampliar.
					</h4>
				</div>
			</div>
			<div class="col-md-10" style="">
				<h3 class="text-muted">
					<span id="name"></span>
				</h3>
				<p>
					<strong>
						<span class="glyphicon glyphicon-duplicate" aria-hidden="true"></span>
						Arquivo
					</strong>
					<span id="type"></span>&nbsp;&nbsp;
					<strong>
						<span class="glyphicon glyphicon-time" aria-hidden="true"></span>
						Criado em
					</strong>
					<span id="created"></span>&nbsp;&nbsp;
					<strong>
						<span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span>
						Tamanho:
					</strong>
					<span id="size"></span>&nbsp;&nbsp;
					<strong>
						<span class="glyphicon glyphicon-resize-full" aria-hidden="true"></span>
						Dimensões:
					</strong>
					<span id="dimensions"></span>
					<br />
					<div class="form-inline">
						<div class="input-group">
							<a class="btn btn-success" id="btn-img-select" href="#">
								<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
								Selecionar
							</a>
						</div>
						<div class="input-group">
							<a class="btn btn-default" id="btn-img-edit" href="#">
								Renomear
							</a>
						</div>
						<div class="input-group">
							<a class="btn btn-default" id="btn-img-delete" href="#">
								Excluir
							</a>
						</div>
						<div class="input-group">
							<a class="btn btn-primary" id="btn-img-download" href="#" download="">
								<span class="glyphicon glyphicon-save" aria-hidden="true"></span>
								Download
							</a>
						</div>
						<div class="input-group">
							<div class="input-group-addon">
								<strong class="visible-lg">
									<span class="glyphicon glyphicon-link" aria-hidden="true"></span>
									Link:
								</strong>
							</div>
							<input type="text" class="form-control" id="path" readonly />
						</div>
					</div>
				</p>
			</div>
		</div>
	</footer>

	<!-- BOOTSTRAP -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<?= Helpers::link_script('bootstrap.min.js') ?>
	<?= Helpers::link_script('bootstrap.fd.js') ?>
	<!-- IMANAGER JS -->
	<?= Helpers::link_script('imanager.js') ?>
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</body>
</html>