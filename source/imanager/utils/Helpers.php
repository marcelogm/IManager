<?php
namespace IManager\Utils;

/**
 * Classe de utilitários utilizada ao longo da execução do programa
 *
 * @version 1.0
 * @author Marcelo Gomes Martins
 */
class Helpers
{
	/**
	 * Recupera a informação de URL base do plugin
	 *
	 * @param mixed $configs referencia de configuração
	 * @return string resposta
	 */
	public static function base_url($configs)
	{
		$hostname = $_SERVER['HTTP_HOST'];
		$protocol = strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, 5));
		if (IMPLUGIN_DEBUG)
		{
			$protocol = strpos($protocol, 'https') ? 'https://' : 'http://';
		}
		else
		{
			// Definido para http://ufrgs.br/cacln
			$protocol = 'https://';
		}
		return $protocol . $hostname . $configs['path'] . '/';
	}

	/**
	 * Retorna um link para um arquivo CSS na estrutura de pastas do plugin
	 * @param mixed $path nome do arquivo
	 * @return string objeto DOM
	 */
	public static function link_css($path)
	{
		return '<link href="' . IMPLUGIN_BASE_URL .
			'imanager/assets/stylesheets/' . $path .
			'" rel="stylesheet">';
	}

	/**
	 * Retorna um link para um arquivo favicon na estrutura de pastas do plugin
	 * @param mixed $path nome do arquivo
	 * @return string objeto DOM
	 */
	public static function link_favicon($path)
	{
		return '<link rel="shortcut icon" href="' .
			IMPLUGIN_BASE_URL .
			'imanager/assets/favicon/favicon.ico" type="image/x-icon" />';
	}

	/**
	 * Retorna um link para um arquivo JS na estrutura de pastas do plugin
	 * @param mixed $path nome do arquivo
	 * @param array $classes chave valor: atributo="valor"
	 * @return string objeto DOM
	 */
	public static function link_script($path, $classes = [])
	{
		return '<script ' . self::generate_classes($classes) . 'src="' . IMPLUGIN_BASE_URL .
			'imanager/assets/scripts/' . $path .
			'"></script>';
	}

	/**
	 * Retorna um link para um arquivo de imagem na estrutura de pastas do plugin
	 * @param mixed $path nome do arquivo
	 * @param array $classes chave valor: atributo="valor"
	 * @return string objeto DOM
	 */
	public static function img($name, $classes = [])
	{
		return '<img ' . self::generate_classes($classes) .
			' src="' . $name . '">';
	}

	/**
	 * Faz a implosão de valores de acordo com o padrão HTML de atributo="valor"
	 * @param array $classes chave valor: atributo="valor"
	 * @return string objeto DOM
	 */
	private static function generate_classes($classes)
	{
		$string = '';
		foreach($classes as $classname => $content)
		{
			$string .= $classname . '="' . $content . '" ';
		}
		return $string;
	}

	/**
	 * Executa funções de autenticação e de verificação de autenticidade de conexão
	 *
	 * @param mixed $secret segredo do Plugin
	 * @param mixed $config referencia de configuração
	 * @return boolean resposta
	 */
	public static function auth($secret, $config)
	{
		return ($secret === $config['secret_key'] && $config['auth_callback']());
	}

	/**
	 * Renderiza conteúdo de determinado arquivo, gerando variaveis locais nesse contexto.
	 *
	 * @param mixed $path arquivo de view
	 * @param array $values valores que serão transformados em variaveis. (vide. PurePHP)
	 * @throws \Exception
	 */
	public static function render($path, $values = [])
	{
		foreach($values as $name => $value)
		{
			$$name = $value;
		}
		if((@include(IMPLUGIN_BASE_PATH . 'views/' . $path . '.php')) === false)
		{
			throw new \Exception($path . ' não foi encontrado.');
		}
	}

	/**
	 * Verifica a integridade de um arquivo
	 * Identifica com base nos valores enviados por parametro
	 * se o arquivo é válido para o procedimento de salvamento.
	 *
	 * @param mixed $size tamanho do arquivo
	 * @param mixed $extension extensão do arquivo
	 * @param mixed $temp_image local temporário do arquivo
	 * @param mixed $config referencia de configuração
	 * @return boolean resposta
	 */
	public static function file_integrity_verification($size, $extension, $temp_image, $config)
	{
		$dimension = getimagesize($temp_image);
		return (
			$dimension !== false &&
			$size <= $config['file_max_size'] &&
			in_array($extension, $config['allowed_ext'])
		);
	}

	/**
	 * Gera um nome aleatório no formato de HASH SHA256
	 * @param mixed $from 
	 * @return string
	 */
	public static function random_name($from)
	{
		return hash('sha256', $from . time() . mt_rand());
	}

	/**
	 * Validador de informações de configuração
	 * 
	 * Função utilziada para facilitar a execução do programa, verificando e
	 * e gerando (se necessário) os valores padrões para que o plugin funcione corretamente
	 *
	 * @param mixed $config referencia de configuração
	 * @throws \Exception possivel erro
	 * @return array resposta
	 */
	public static function config_validation($config)
	{
		if(is_array($config))
		{
			$config['debug'] = self::is_set_in($config, 'debug');
			$config['path'] = self::is_set_in($config, 'path', 'app\\plugins\\sources\\');
			$config['secret_key'] = self::is_set_in($config, 'secret_key', hash('sha256', 'implugin'));
			$config['random_name'] = self::is_set_in($config, 'random_name');
			$config['file_max_size'] = self::is_set_in($config, 'file_max_size', 4194304);
			$config['allowed_ext'] = self::is_set_in(
				$config,
				'allowed_ext',
				['png', 'jpeg', 'jpg', 'gif'],
				function ($v) { return is_array($v); }
			);
			$config['file_hash_type'] = self::is_set_in($config, 'file_hash_type', 'sha256');
			$config['auth_callback'] = self::is_set_in(
				$config,
				'auth_callback',
				function () { return true; },
				function ($v) { return is_callable($v); }
			);
			$config['user_id_callback'] = self::is_set_in(
				$config,
				'user_id_callback',
				function () { return 0; },
				function ($v) { return is_callable($v); }
			);
			$config['image'] = self::is_set_in(
				$config,
				'image', [],
				function ($v) { return is_array($v); }
			);
			$config['image']['resize'] = self::is_set_in($config['image'], 'resize');
			$config['image']['crop'] = self::is_set_in($config['image'], 'crop');
			$config['image']['size'] = self::is_set_in(
				$config['image'],
				'size',
				['width' => 200, 'height' => 200],
				function($v)
				{
					return (is_array($v) && isset($v['width']) && isset($v['width']));
				}
			);
			$config['thumb'] = self::is_set_in(
				$config,
				'thumb',
				[],
				function ($v) { return is_array($v); }
			);
			$config['thumb']['use'] = self::is_set_in($config['thumb'], 'use');
			$config['thumb']['crop'] = self::is_set_in($config['thumb'], 'crop');
			$config['thumb']['size'] = self::is_set_in(
				$config['thumb'],
				'size',
				['width' => 200, 'height' => 300],
				function($v)
				{
					return (is_array($v) && isset($v['width']) && isset($v['width']));
				}
			);
			return $config;
		}
		else
		{
			throw new \Exception('Array de configuração (IManager\Config::general()) é inválido.');
		}
	}

	/**
	 * Utilizada pelo validador de configurações,
	 * verifica se determinado valor está presente em uma key de um array,
	 * caso não esteja, define um valor padrão
	 *
	 * @internal
	 * @param mixed $value array
	 * @param mixed $key indice dentro desse array
	 * @param mixed $default valor padrão
	 * @param mixed $callback função extra de validação 
	 * @return mixed reposta
	 */
	public static function is_set_in($value, $key, $default = false, $callback = null)
	{
		if ($callback === null)
		{
			$callback = function($v) { return true; };
		}
		if (isset($value[$key]) && $callback($value[$key]))
		{
			return $value[$key];
		}
		return $default;
	}

	/**
	 * Formata os valores de tamanho de arquivos de bytes
	 * para uma unidade de medida maior (KB, MB, GB, TB)
	 *
	 * @param mixed $bytes tamanho
	 * @param mixed $precision casas decimais
	 * @return string
	 */
	public static function format_bytes($bytes, $precision = 2) {
		$units = array('B', 'KB', 'MB', 'GB', 'TB');

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		$bytes /= pow(1024, $pow);

		return round($bytes, $precision) . ' ' . $units[$pow];
	}

}