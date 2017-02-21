<?php
namespace IManager;
use IManager\Configs\Config;
use IManager\Repository\Image;
use IManager\Repository\File;
use IManager\Utils\Helpers;

/**
 * Classe principal do Plugin
 *
 * * @version 1.0
 * @author Marcelo Gomes Martins
 */
class IMPlugin
{

	private $configs;
	private $directories;
	private $database;

	/**
	 * Método construtor do IMPlugin
	 * Define constantes que serão utilizadas ao longo do programa
	 * elas podem ser definidas antes pelo usuário e não serão alteradas.
	 * Também recupera os dados de configuração e valida-os.
	 */
	public function __construct()
	{
		$this->configs = Helpers::config_validation(Config::general());
		$this->directories = Config::directories();
		if(!defined('IMPLUGIN_BASE_PATH'))
		{
			define('IMPLUGIN_BASE_PATH', __DIR__ . '/');
		}
		if(!defined('IMPLUGIN_DEBUG'))
		{
			define('IMPLUGIN_DEBUG', $this->configs['debug']);
		}
		if(!defined('IMPLUGIN_BASE_URL'));
		{
			define('IMPLUGIN_BASE_URL', Helpers::base_url($this->configs));
		}
	}

	/**
	 * Registra (em execuções fora do PurePHP) um autoloader próprio
	 * para carregamento da estrutura de pastas do Plugin.
	 */
	public static function autoloader_registration()
	{
		define('IMPLUGIN_BASE_PATH', __DIR__ . '/');
		spl_autoload_register(
			function ($classname)
			{
				$classname = ltrim($classname, '\\');
				$filename  = '';
				$namespace = '';
				if ($lastnspos = strripos($classname, '\\'))
				{
					$namespace = substr($classname, 0, $lastnspos);
					$namespace = strtolower($namespace);
					$namespace = str_replace('imanager\\', '', $namespace);
					$classname = substr($classname, $lastnspos + 1);
					$filename  = str_replace('\\', '/', $namespace) . '/';
				}
				$filename .= str_replace('_', '/', $classname) . '.php';
				$to_require = IMPLUGIN_BASE_PATH . $filename;
				if (!file_exists($to_require))
				{
					return false;
				}
				require_once($to_require);
				return true;
			}
		);
	}

	/**
	 * Responsável por lidar com as requisições feitas pelo próprio plugin
	 * geralmente destinadas a execução de tarefas de upload, edição, deleção
	 * e carregamento de informações
	 *
	 * Requer que exista um valor válido em $_POST['implugin-selected-action']
	 * que irá definir um fluxo de execução e $_POST['implugin-secret-key'] (definida em secret_key de Config)
	 * permitindo que requisições só venham a partir do Plugin.
	 *
	 * Executará o método de autenticação com a secret-key e as configurações definidas pelo usuário.
	 *
	 * @see configs/Config.php
	 */
	public function api()
	{
		$action = isset($_POST['implugin-selected-action']) ? $_POST['implugin-selected-action'] : null;
		$secret = isset($_POST['implugin-secret-key']) ? $_POST['implugin-secret-key'] : null;

		if(Helpers::auth($secret, $this->configs)){
			switch($action)
			{
				case 'upload-image':
					$this->api_upload_files();
					exit();
				case 'delete-image':
					$this->api_delete();
					exit();
				case 'edit-image':
					$this->api_edit();
					exit();
				case 'load-image-list':
					$this->api_load_image_list();
					exit();
				default:
					break;
			}
		}
		http_response_code(400);
		exit();
	}

	/**
	 * Responde a requisição de upload de arquivo realizada pela API
	 *
	 * Recupera os arquivos da variavel $_FILE['implugin-file-$i'] gerada pelo Plugin,
	 * executa o método save para cada arquivo
	 * @internal
	 */
	private function api_upload_files()
	{
		$count = count($_FILES);
		for($i = 0; $i < $count; $i++)
		{
			$response = $this->save($_FILES['implugin-file-' . $i]);
			/// @todo
			/*if ($response === false)
			{
				break;
			}*/
		}
		Helpers::render('ajax/response', [
			'title' => 'Imagens enviadas!',
			'body' => 'Todas as imagens foram enviadas com sucesso!'
		]);
	}

	/**
	 * Responde a requisição de deleção de arquivo realizada pela API
	 * @todo
	 */
	private function api_delete()
	{
		$data = [];
		$id = isset($_POST['implugin-id']) ? intval($_POST['implugin-id']) : false;
		$confirmation = isset($_POST['implugin-confirm-action']) ? $_POST['implugin-confirm-action'] : false;

		if($id)
		{
			if ($confirmation === false)
			{
				$image = Image::find($id);
				if ($image)
				{
					Helpers::render('ajax/delete', ['image' => $image]);
					exit();
				}
				$data = ['title' => 'Falha ao excluir.', 'body' => 'Não há nenhuma imagem com essas informações.'];
			}
			else if ($confirmation && $this->delete($id))
			{
				$data = ['title' => 'Imagem excluída.', 'body' => 'A imagem foi excluída com sucesso.'];
			}
			else
			{
				$data = ['title' => 'Falha ao excluir.', 'body' => 'Não há nenhuma imagem com essas informações.'];
			}
			Helpers::render('ajax/response', $data);
			exit();
		}
		http_response_code(400);
		exit();
	}

	/**
	 * Responde a requisição de edição de arquivo realizada pela API
	 * @todo
	 */
	private function api_edit()
	{
		$data = [];
		$id = isset($_POST['implugin-id']) ? intval($_POST['implugin-id']) : false;
		$name = isset($_POST['implugin-name']) ? $_POST['implugin-name'] : false;
		$confirmation = isset($_POST['implugin-confirm-action']) ? $_POST['implugin-confirm-action'] : false;

		if($id)
		{
			if ($confirmation === false)
			{
				$image = Image::find_complete_information($id);
				if ($image)
				{
					Helpers::render('ajax/edit', ['image' => $image]);
					exit();
				}
				$data = ['title' => 'Falha ao renomear.', 'body' => 'Não há nenhuma imagem com essas informações.'];
			}
			else if ($confirmation && $this->edit($id, $name))
			{
				$data = ['title' => 'Imagem excluída.', 'body' => 'A imagem foi excluída com sucesso.'];
			}
			else
			{
				$data = ['title' => 'Falha ao renomear.', 'body' => 'Não há nenhuma imagem com essas informações.'];
			}
			Helpers::render('ajax/response', $data);
			exit();
		}
		http_response_code(400);
		exit();
	}

	/**
	 * Responde a requisição de listagem de arquivos realizada pela API
	 *
	 * Recupera as informações de pesquisa e realiza a busca em banco de dados
	 *
	 * @todo opção sem thumbnail
	 */
	private function api_load_image_list()
	{
		$PER_PAGE = 24;
		$message = null;
		$search = isset($_POST['implugin-search']) ? trim($_POST['implugin-search']) : null;
		$page = isset($_POST['implugin-page']) ? intval($_POST['implugin-page']) : 0;
		$offset = ($page * $PER_PAGE);
		$images = Image::select($PER_PAGE, $offset, explode(' ', $search));
		$count = Image::count($search);
		$has_more = false;
		$page++;
		if($offset + $PER_PAGE < $count)
		{
			$has_more = true;
		}

		if ($images) {
			foreach($images as &$image)
			{
				$image->url = IMPLUGIN_BASE_URL . 'imanager/' . $this->directories['base_dir'] . $image->path . '.' . $image->type;
				$image->thumb = IMPLUGIN_BASE_URL . 'imanager/' . $this->directories['thumb_dir'] . $image->path . '.' . $image->type;
			}
		} else
		{
			$message = 'Nenhuma imagem foi encontrada.';
		}

		Helpers::render('ajax/list', [
			'images' => $images,
			'message' => $message,
			'more' => $has_more,
			'next' => $page,
			'search' => $search,
			'count' => $count
 		]);
	}

	/**
	 * Renderiza o navegador de imagens padrão do framework
	 */
	public function browse()
	{
		if($this->configs['auth_callback']())
		{
			Helpers::render('browser', [
				'secret' => $this->configs['secret_key']
			]);
			exit();
		}
		http_response_code(400);
	}

	/**
	 * Salva imagem a partir de valor na variavel $_FILES.
	 *
	 * Chama save_procedure
	 *
	 * @param mixed $image_information indice de $_FILES com as informações de um arquivo
	 * @return \boolean|string path salvo ou false
	 */
	private function save($image_information)
	{
		$file_name = basename($image_information['name']);
		$file_size = $image_information['size'];
		$file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
		$temp_name = $image_information['tmp_name'];
		if ($this->configs['random_name'])
		{
			$hash_name = Helpers::random_name($file_name);
		}
		if (Helpers::file_integrity_verification($file_size, $file_extension, $temp_name, $this->configs))
		{
			// @todo local para salvar deve estar disponivel
			return $this->save_procedure(
				$file_name,
				$temp_name,
				isset($hash_name) ? $hash_name : $file_name,
				$file_extension,
				$file_size
			);
		}
		return false;
	}

	/**
	 * Realiza o procedimento de salvamento propriamente dito
	 *
	 * @param mixed $original_name nome original do arquivo
	 * @param mixed $source local onde ele se encontra temporariamente
	 * @param mixed $file_name nome do arquivo em hash
	 * @param mixed $file_extension extensão do arquivo
	 * @param mixed $size tamanho do arquivo
	 * @return string path onde foi salvo
	 */
	private function save_procedure($original_name, $source, $file_name, $file_extension, $size)
	{
		$hash = hash_file($this->configs['file_hash_type'], $source);
		$destination = IMPLUGIN_BASE_PATH . $this->directories['base_dir'] . $file_name;
		$file = File::exists($hash);
		// O arquivo existe?
		if ($file === false)
		{
			// Refatorar imagem?
			if($this->configs['image']['refactor'] == true)
			{
				// Corta arquivo
				$image = $this->resize_image(
					$source,
					$this->configs['image']['size']['width'],
					$this->configs['image']['size']['height'],
					$this->configs['image']['crop']
				);
				// Salva de acordo com a extensão padrão, caso exista
				// Gera um arquivo físico
				if (isset($this->configs['image']['output']))
				{
					$file_extension = $this->save_with_file_extension($image, $destination, $this->configs['image']['output']);
				} else {
					$file_extension = $this->save_with_file_extension($image, $destination, $file_extension);
				}
			} else {
				// Gera um arquivo físico
				move_uploaded_file($source, $destination . '.' . $file_extension);
			}
			list($width, $heigth) = getimagesize($destination . '.' . $file_extension);
			// Persiste informações do arquivo em banco
			$file = File::save($file_extension, $hash, $size, $width, $heigth);
			// Gera uma thumb, se for o caso
			if($this->configs['thumb']['use'])
			{
				$thumb = $this->resize_image(
					$destination . '.' . $file_extension,
					$this->configs['thumb']['size']['width'],
					$this->configs['thumb']['size']['height'],
					$this->configs['thumb']['crop']
				);
				imagepng($thumb, IMPLUGIN_BASE_PATH . $this->directories['thumb_dir'] . $file_name . '.' . $file_extension);
			}
		} else
		{
			// Recupera dados do arquivo
			$image = Image::find_by_file($file);
			$file_source = IMPLUGIN_BASE_PATH . $this->directories['base_dir'] . $image->path . '.' . $image->type;
			$thumb_source = IMPLUGIN_BASE_PATH . $this->directories['thumb_dir'] . $image->path . '.' . $image->type;
			$thumb_destination = IMPLUGIN_BASE_PATH . $this->directories['thumb_dir'] . $file_name . '.' . $image->type;
			// Gera o link físico no diretório
			$this->link($file_source, $destination . '.' . $image->type);
			$this->link($thumb_source, $thumb_destination);
		}
		// Persiste informações em imagem
		Image::save(pathinfo($original_name, PATHINFO_FILENAME), $file_name, $file, $this->configs['user_id_callback']());
		return $file_name;
	}

	/**
	 * Prepara o procedimento de edição (renomear)
	 *
	 * @param mixed $id id do arquivo
	 * @param mixed $name novo nome para o arquivo
	 * @return boolean resposta
	 */
	private function edit($id, $name)
	{
		// @todo find_all
		$image = Image::find_complete_information(intval($id));
		if(isset($image->id) && $name !== ''  && is_string($name))
		{
			return $this->edit_procedure($image, $name);
		}
		return false;
	}

	/**
	 * Realiza o procedimento de edição propriamente dito
	 *
	 * @param mixed $image dados da imagem
	 * @param mixed $new_name novo nome para o arquivo
	 * @return boolean resposta
	 */
	private function edit_procedure($image, $new_name)
	{
		return Image::edit($image->id, $new_name);
	}

	/**
	 * Prepara o procedimento de exclusão
	 *
	 * @param mixed $id id do arquivo
	 * @return boolean resposta
	 */
	private function delete($id)
	{
		$image = Image::find_complete_information(intval($id));
		if(isset($image->id))
		{
			return $this->delete_procedure($image);
		}
		return false;
	}

	/**
	 * Realiza o procedimento de exclusão propriamente dito
	 *
	 * @param mixed $image dados da imagem
	 * @param mixed $new_name novo nome para o arquivo
	 * @return boolean resposta
	 */
	private function delete_procedure($image)
	{

		if(Image::delete($image->id))
		{
			// Refazer de modo que consiga indetificar se existe outra imagem
			// para poder excluir o File de fato
			if(Image::find_by_file($image->file) === null)
			{
				File::delete($image->file);
			}
			@unlink(IMPLUGIN_BASE_PATH . $this->directories['base_dir'] . $image->path . '.' . $image->type);
			@unlink(IMPLUGIN_BASE_PATH . $this->directories['thumb_dir'] . $image->path . '.' . $image->type);
			return true;
		}
		return false;
	}

	/**
	 * Gera um thumbnail e retorna o recurso
	 *
	 * @param mixed $file arquivo original
	 * @param mixed $width largura máxima
	 * @param mixed $heigth altura máxima
	 * @param mixed $crop cortar?
	 * @return resource
	 */
	private function resize_image($file, $width, $heigth, $crop = false)
	{
		list($cur_width, $cur_heigth) = getimagesize($file);
		$r = $cur_width / $cur_heigth;
		if($crop)
		{
			if($cur_width > $cur_heigth)
			{
				$cur_width = ceil($cur_width - ($cur_width * abs($r - $width / $heigth)));
			} else
			{
				$cur_heigth = ceil($cur_heigth - ($cur_heigth * abs($r - $width / $heigth)));
			}
			$new_width = $width;
			$new_height = $heigth;
		}
		else
		{
			if($width / $heigth > $r)
			{
				$new_width = $heigth * $r;
				$new_height = $heigth;
			}
			else
			{
				$new_height = $width / $r;
				$new_width = $width;
			}
		}
		$source = imagecreatefromstring(file_get_contents($file));
		$destination = imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($destination, $source, 0, 0, 0, 0, $new_width, $new_height, $cur_width, $cur_heigth);
		return $destination;
	}

	/**
	 * Gera link físico de um arquivo em Windows ou Linux
	 *
	 * @param mixed $source origem
	 * @param mixed $target destino
	 * @return mixed destino ou false
	 */
	private function link($source, $target)
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			if(function_exists('link'))
			{
				link($source, $target);
				return $target;
			}
		} else {
			if(function_exists('shell_exec'))
			{
				shell_exec('ln ' . $source . ' ' . $target);
				return $target;
			}
		}
		return false;
	}

	/**
	 * Salva uma imagem em forma de resource com determinado tipo de arquivo
	 *
	 * @param resource $image
	 * @param string $destination
	 * @param string $type
	 * @throws \Exception Caso o tipo de arquivo não seja válido
	 * @return string extensão atual do arquivo
	 */
	private function save_with_file_extension($image, $destination, $type)
	{
		$ext = '';
		switch ($type)
		{
			case 'png':
				imagepng($image, $destination . '.png');
				$ext = 'png';
				break;
			case 'jpg':
			case 'jpeg':
				imagejpeg($image, $destination . '.jpg', 100);
				$ext = 'jpg';
				break;
			case 'gif':
				imagegif($image, $destination  . '.gif');
				$ext = 'gif';
				break;
			case 'bmp':
				imagebmp($image, $destination  . '.bmp');
				$ext = 'bmp';
				break;
			case 'wbmp':
				imagebmp($image, $destination  . '.wbmp');
				$ext = 'wbmp';
				break;
			default:
				throw new \Exception('O tipo de arquivo de saída não é válido. Em Config::general()[\'image\'][\'output\'].');
		}
		return $ext;
	}

}