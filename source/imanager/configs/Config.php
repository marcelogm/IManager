<?php
namespace IManager\Configs;

/**
 * Classe que gerencia configurações globais do Plugin
 *
 * @version 1.0
 * @author Marcelo Gomes Martins
 */
class Config
{

	/**
	 * Método que retorna as principais informações e configurações
	 * utilizadas ao longo do plugin.
	 * @return array informações
	 */
	public static function general()
	{
		$secret_key = hash('sha256', 'change_it');
		return [
			// O Plugin está em desenvolvimento? Default: false
			'debug' => true,
			// Caminho relativo do gerenciador na aplicação. Default: 'app/plugins/sources/'
			'path' => 'imanager/source/',
			// Palavra secreta utilizada para verificar autenticidade da requisição Default: hash('sha256', 'implugin')
			'secret_key' => $secret_key,
			// Randomizar nome das imagens? Default: false
			'random_name' => true,
			// Tamanho máximo dos arquivos. Default: 4194304
			'file_max_size' => 4194304,
			// Tipos de arquivos permitidos. Default: ['png', 'jpeg', 'jpg', 'gif']
			'allowed_ext' => ['png', 'jpeg', 'jpg', 'gif'],
			// Tipo de algoritmo de hash. Default: 'sha256'
			'file_hash_type' => 'sha256',
			// Callback de autenticação
			// Permite verificar de acordo com critérios personalizados
			// se o usuário está autorizado a acessar o gerenciador de imagem
			// Default: function () { return true; }
			'auth_callback' => function()
			{
				$name = hash('sha256', 'CustomSession' .
					$_SERVER['REMOTE_ADDR'] .
					$_SERVER['HTTP_USER_AGENT']);
				session_name($name);
				session_cache_expire(10);
				if (session_status() == PHP_SESSION_NONE) {
					session_start();
				}
				return isset( $_SESSION['uid']);
			},
			// Callback de id de usuário
			// Permite atribuir a autoria de uma imagem a um ID de um usuário
			// Default: function () { return 0; }
			'user_id_callback' => function()
			{
				$name = hash('sha256', 'CustomSession' .
					$_SERVER['REMOTE_ADDR'] .
					$_SERVER['HTTP_USER_AGENT']);
				session_name($name);
				session_cache_expire(10);
				if (session_status() == PHP_SESSION_NONE) {
					session_start();
				}
				return $_SESSION['uid'];
			},
			// Configurações das imagens
			'image' => [
				// Padronizar imagem? Define um tamanho e formato padrão. Default: false
				'refactor' => false,
				// Cortar imagem? Default: false
				'crop' => false,
				// Tamanho desejado. Default: ['width' => 1200, 'height' => 1200]
				'size' => ['width' => 1500, 'height' => 1500],
				// Tipo de saída de arquivo padronizada. Default: não existente (aceitaveis: jpeg, gif, png, wmp ou wbmp)
				//'output' => 'png'
			],
			// Configurações de thumbnail
			'thumb' => [
				// Criar thumbnail? Default: false
				'use' => true,
				// Cortar imagem? Default: false
				'crop' => false,
				// Tamanho desejado. Default: ['width' => 200, 'height' => 200]
				'size' => ['width' => 300, 'height' => 300]
			]
		];
	}

	/**
	 *  Método que retorna as principais informações de diretórios
	 * @return string[]
	 */
	public static function directories()
	{
		return [
			'relative' => IMPLUGIN_BASE_PATH,
			'base_dir' => 'uploads/',
			'thumb_dir' => 'uploads/thumbs/'
		];
	}

	/**
	 *  Método que retorna as principais configurações de banco de dados
	 * @return string[]
	 */
	public static function database()
	{
		if(IMPLUGIN_DEBUG)
		{
			return [
				'address' => 'localhost',
				'port' => '3306',
				'dbname' => 'cacln',
				'username' => 'root',
				'password' => '',
				'create_table' => true
			];
		}
		return [
			'address' => '',
			'port' => '',
			'dbname' => '',
			'username' => '',
			'password' => '',
		    'create_table' => true
		];
	}

}