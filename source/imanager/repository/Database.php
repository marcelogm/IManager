<?php
namespace IManager\Repository;
use IManager\Configs\Config;
use PDO;

/**
 * Banco de Dados
 *
 * Classe representativa do banco de dados.
 * Gera conexão e estabelece funcionalidades básicas do banco de dado.
 *
 * @version 1.0
 * @author Marcelo Gomes Martins
 */
class Database
{
    private static $instance = null;
	private $connection;
	private $config;

	/**
	 * Método construtor
	 * Executa a validação de campos de configurações,
	 * bem como inicia conexão com o PDO.
	 *
	 * @access privado para proibir novas instances.
	 * @internal método de uso interno
	 */
	private function __construct()
	{
		$this->config = Config::database();
		try {
			$this->connection = new PDO(
				'mysql:host=' . $this->config['address'] .
				';port=' . $this->config['port'] .
				';dbname=' . $this->config['dbname'] ,
				$this->config['username'],
				$this->config['password']
			);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			if ($this->config['create_table'])
			{
				$this->create_table();
			}
		}
		catch (PDOException $e)
		{
			throw new \Exception('Arquivo de configuração inválido.');
		}
	}

	private function __clone(){}
	private function __wakeup(){}

	/**
	 * Recupera a única instance disponível do objeto
	 *
	 * @see Singleton
	 * @link https://pt.wikipedia.org/wiki/Singleton
	 * @return Database
	 */
	public static function get_instance()
	{
		if (self::$instance === null)
		{
			self::$instance = new Database();
		}
		return self::$instance;
	}

	/**
	 * Valida configurações da classe Config::database(),
	 * verificando se o objeto é um array e tem todos os itens
	 * necessarios para configurar o banco de dados.
	 *
	 * @param string[] $config
	 * @return boolean resposta
	 */
	private function config_validation($config)
	{
		return (is_array($config) &&
			array_key_exists('address', $config) &&
			array_key_exists('port', $config) &&
			array_key_exists('dbname', $config) &&
			array_key_exists('username', $config) &&
			array_key_exists('password', $config));
	}

	/**
	 * Executa determinada query no bancao de dados
	 *
	 * Ex.: 'SELECT * FROM database WHERE id = :id'
	 *
	 * @param mixed $query a ser executada
	 * @param mixed $params parametros que serão interpolados
	 * @throws \Exception possivel exception
	 * @return array resposta
	 */
	public function execute_query($query, $params = [])
	{
		$list = [];
		try {
			$statement = $this->connection->prepare($query);
			foreach($params as $key => $value)
			{
				$statement->bindValue($key, $value);
			}
			$statement->execute();
			while($object = $statement->fetchObject())
			{
				array_push($list, $object);
			}
			return $list;
		}
		catch(\Exception $e)
		{
			throw new \Exception('Falha ao executar query: ' .
				$query .
				' Mais informações: ' .
				$e->getMessage()
			);
		}
	}


	/**
	 * Executa determinada inserção de valores o banco de dados
	 *
	 * Ex.: 'INSERT INTO database(...) VALUES(...)'
	 *
	 * @param mixed $query a ser executada
	 * @param mixed $params parametros que serão interpolados
	 * @throws \Exception possivel exception
	 * @return array resposta
	 */
	public function execute_update($query, $params = [])
	{
		try {
			$statement = $this->connection->prepare($query);
			foreach($params as $key => $value)
			{
				$statement->bindValue($key, $value);
			}
			$result = $statement->execute();
			$id = $this->connection->lastInsertId('id');
			return (!$result) ? false : $id;
		}
		catch(\Exception $e)
		{
			throw new \Exception('Falha ao executar inserção: ' .
				$query .
				' Mais informações: ' .
				$e->getMessage()
			);
		}
	}

	/**
	 * Inicia transação
	 * @return boolean
	 */
	public function begin()
	{
		return $this->connection->beginTransaction();
	}

	/**
	 * Finaliza transação
	 * @return boolean
	 */
	public function commit()
	{
		return $this->connection->commit();
	}

	/**
	 * Retoma caso ocorra alguma falha
	 * @return boolean
	 */
	public function rollback()
	{
		return $this->connection->rollBack();
	}

	/**
	 * Executa criação do banco de dados de imagens
	 * caso ele não exista.
	 */
	private function create_table()
	{
		$script =
'CREATE TABLE IF NOT EXISTS `implugin_file` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(6) NOT NULL,
  `hash` varchar(64) NOT NULL,
  `size` bigint(20) UNSIGNED NOT NULL,
  `width` smallint(5) UNSIGNED NOT NULL,
  `height` smallint(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `HASH` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `implugin_image` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `path` varchar(64) NOT NULL,
  `file` bigint(20) UNSIGNED NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `owner` bigint(20) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `OWNER_ID` (`owner`),
  KEY `file` (`file`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `implugin_image`
  ADD CONSTRAINT `FILE_IMAGE` FOREIGN KEY (`file`) REFERENCES `implugin_file` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;';
		$this->execute_update($script);
	}

}