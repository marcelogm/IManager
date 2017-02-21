<?php
namespace IManager\Repository;
use IManager\Repository\Database;
use IManager\Repository\File;

/**
 * Repositório de Imagens presentes no banco de dados
 *
 * @version 1.0
 * @author Marcelo Gomes Martins
 */
class Image
{

	public static $TABLE_NAME = '`implugin_image`';

	/**
	 * Busca imagem no banco de dados apartir de um id
	 * @param mixed $id id que será buscado
	 * @return mixed resposta
	 */
	public static function find($id)
	{
		$database = Database::get_instance();
		try {
			$query = 'SELECT * FROM ' . self::$TABLE_NAME . ' WHERE id = :id';
			$result = $database->execute_query($query, [
				':id' => $id
			]);
			return isset($result[0]) ? $result[0] : false;
		}
		catch(\Exception $e)
		{
			return false;
		}
	}

	/**
	 * Busca uma imagem (juntamente com seus dados de arquivo) com a
	 * referencia para um arquivo (id de File)
	 *
	 * @param mixed $id
	 * @return mixed
	 */
	public static function find_by_file($id)
	{

		$database = Database::get_instance();
		try {
			$query = 'SELECT * FROM ' . self::$TABLE_NAME .
				' JOIN ' . File::$TABLE_NAME .
				' ON ' . self::$TABLE_NAME . '.`file` = '.
				File::$TABLE_NAME . '.`id` ' .
				' WHERE ' . self::$TABLE_NAME . '.`file` = :id LIMIT 1';
			$result = $database->execute_query($query, [
				':id' => $id
			]);
			return isset($result[0]) ? $result[0] : null;
		}
		catch(\Exception $e)
		{
			return false;
		}
	}

	/**
	 * Salva determinado imagem
	 * @param mixed $name nome da imagem
	 * @param mixed $path local relativo do arquivo
	 * @param mixed $file_id id do arquivo
	 * @param mixed $user_id id do usuário
	 * @return \array|boolean
	 */
	public static function save($name, $path, $file_id, $user_id)
	{
		$database = Database::get_instance();
		try {
			$query = 'INSERT INTO ' . self::$TABLE_NAME . ' (`name`, `path`, `file`, `owner`) '.
					 'VALUES (:name, :path, :file, :owner);';
			$result = $database->execute_update($query, [
				':name' => $name,
				':path' => $path,
				':file' => $file_id,
				':owner' => $user_id
			]);
			return $result;
		}
		catch(\Exception $e)
		{
			return false;
		}
	}

	/**
	 * Seleciona todas as imagens entre determinado limite e deslocamento
	 * possibilita o envio de um filtro para o nome
	 *
	 * @todo melhorar perfomance da query
	 * @param mixed $limit limite de itens por query
	 * @param mixed $offset deslocamento dentro desse limite
	 * @param mixed $like filtro de nome utilizando WHERE ... LIKE
	 * @return \array|boolean resposta
	 */
	public static function select($limit, $offset, $like = null)
	{
		$database = Database::get_instance();
		try {
			$query = 'SELECT ' . self::$TABLE_NAME . '.`id`,' .
				self::$TABLE_NAME . '.`path`,' .
				File::$TABLE_NAME . '.`type`, ' .
				File::$TABLE_NAME . '.`hash`, ' .
				File::$TABLE_NAME . '.`size`, ' .
				self::$TABLE_NAME . '.`name`, ' .
				File::$TABLE_NAME . '.`width`, ' .
				File::$TABLE_NAME . '.`height`, ' .
				self::$TABLE_NAME . '.`created`, ' .
				self::$TABLE_NAME . '.`owner` FROM ' .
				self::$TABLE_NAME . ' JOIN ' .
				File::$TABLE_NAME . ' ON ' .
				File::$TABLE_NAME . '.`id` = ' .
				self::$TABLE_NAME . '.`file` ';
			if(is_string($like))
			{
				$query .= ' WHERE ' . self::$TABLE_NAME . '.`name` LIKE \'%' . $like . '%\'';
			}
			else if (is_array($like))
			{
				$query .= ' WHERE ' . self::$TABLE_NAME . '.`name` LIKE \'%' .
					implode( '%\' AND ' . self::$TABLE_NAME . '.`name` LIKE \'%', $like) . '%\'';
			}
			$query .= ' ORDER BY ' . self::$TABLE_NAME . '.`created` DESC';
			$query .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
			$result = $database->execute_query($query);
			return $result;
		}
		catch(\Exception $e)
		{
			return false;
		}
	}

	/**
	 * Conta quantas itens satisfazem a pesquisa.
	 *
	 * @param mixed $like
	 * @return mixed
	 */
	public static function count($like)
	{
		$database = Database::get_instance();
		try {
			$query = 'SELECT COUNT(*) AS `count` FROM ' . self::$TABLE_NAME;
			if(is_string($like))
			{
				$query .= ' WHERE ' . self::$TABLE_NAME . '.`name` LIKE \'%' . $like . '%\'';
			}
			else if (is_array($like))
			{
				$query .= ' WHERE ' . self::$TABLE_NAME . '.`name` LIKE \'%' .
					implode( '%\' AND ' . self::$TABLE_NAME . '.`name` LIKE \'%', $like) . '%\'';
			}
			$result = $database->execute_query($query);
			return $result[0]->count;
		}
		catch(\Exception $e)
		{
			return false;
		}
	}

	/**
	 * Executa a exclusão de imagem pelo id
	 * @param mixed $id id da imagem
	 * @return boolean
	 */
	public static function delete($id)
	{
		$database = Database::get_instance();
		try {
			$query = 'DELETE FROM ' . self::$TABLE_NAME .
					' WHERE `id` = :id;';
			$database->execute_update($query, [
				':id' => $id,
			]);
			return true;
		}
		catch(\Exception $e)
		{
			return false;
		}
	}

	/**
	 * Renomeia imagem de acordo com o id
	 * @param mixed $id id da imagem
	 * @param mixed $name novo nome
	 * @return boolean
	 */
	public static function edit($id, $name)
	{
		$database = Database::get_instance();
		try {
			$query = 'UPDATE ' . self::$TABLE_NAME .
					' SET `name` = :name WHERE `id` = :id';
			$database->execute_update($query, [
				':id' => $id,
				':name' => $name
			]);
			return true;
		}
		catch(\Exception $e)
		{
			return false;
		}
	}

	/**
	 * Recupera informações da imagem e do arquivo utilizando o id da imagem
	 *
	 * @param mixed $id id da imagem
	 * @return mixed
	 */
	public static function find_complete_information($id)
	{
		$database = Database::get_instance();
		try {
			$query = 'SELECT ' . self::$TABLE_NAME . '.`id`,' .
				self::$TABLE_NAME . '.`path`,' .
				File::$TABLE_NAME . '.`type`, ' .
				File::$TABLE_NAME . '.`hash`, ' .
				File::$TABLE_NAME . '.`size`, ' .
				File::$TABLE_NAME . '.`id` AS `file`, ' .
				self::$TABLE_NAME . '.`name`, ' .
				File::$TABLE_NAME . '.`width`, ' .
				File::$TABLE_NAME . '.`height`, ' .
				self::$TABLE_NAME . '.`created`, ' .
				self::$TABLE_NAME . '.`owner` FROM ' .
				self::$TABLE_NAME . ' JOIN ' .
				File::$TABLE_NAME . ' ON ' .
				File::$TABLE_NAME . '.`id` = ' .
				self::$TABLE_NAME . '.`file` ' .
				' WHERE ' . self::$TABLE_NAME . '.`id` = :id LIMIT 1';
			$result = $database->execute_query($query, [
				':id' => $id
			]);
			return isset($result[0]) ? $result[0] : null;
		}
		catch(\Exception $e)
		{
			return false;
		}
	}

}