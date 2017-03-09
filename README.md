# IManager
Gerenciador de imagens para CKEditor

O IManager é um plugin de gerenciamento de imagens para uso em aplicações de pequeno porte.

##Vantagens (Em edição)

##Como utilizar

1.	Fazer o download do arquivo imanager-dist-1.0.2.zip e descompactar.
2.	Mover o conteúdo da pasta dist caminho que o plugin responderá.
3.  Faça as modificações desejadas no arquivo de configuração presente em *imanager\configs\Config.php*.
4.	Caso utilize algum framework, pode-se utilizar a método de auto carregamento na rota desejada:
```php
include('imanager\IMPlugin.php');
IMPlugin::autoloader_registration();
$manager = new IMPlugin();
$manager->browse();
```
4.	Para integrar ao CKEditor, modifique script de inicialização do editor:
```js
CKEDITOR.replace(‘editor’, {
  filebrowserBrowseUrl: 'http://domain.com/imanager/index.php',
  filebrowserWindowWidth: '1250',
  filebrowserWindowHeight: '800'
});
```
##Caso utilize o framework PurePHP:

1.	Mover o conteúdo de *dist\imanager* para *app\plugins\sources\imanager*, dentro da estrutura de pastas do framework.
2.	Fazer o download do arquivo imanager-purephp e descompactar em app\plugins.
3.	Crie uma instancia do gerenciador de imagem em uma rota, conforme exemplo abaixo:
```php
<?php
namespace App\Controllers;
use Pure\Bases\Controller;
use App\Plugins\ImageManager;
use Pure\Utils\Auth;
use Pure\Utils\Request;

/**
 * Controller de upload de imagem
 */
class ImageController extends Controller
{

	public function browse_action()
	{
		$manager = ImageManager::create();
		$manager->browse();
	}
	
	public function before()
	{
		if (!Auth::is_authenticated())
		{
			Request::redirect('error/index');
		}
	}
}
```
4.	Para integrar ao CKEditor, modifique script de inicialização do editor, conforme:
```js
CKEDITOR.replace(‘editor’, {
  filebrowserBrowseUrl: '<?= DynamicHtml::link_to('image/browse') ?>',
  filebrowserWindowWidth: '1250',
	filebrowserWindowHeight: '800'
});
```
Obs.: em filebrowserBrowseUrl coloque a URL a qual o plugin estará acessível.
