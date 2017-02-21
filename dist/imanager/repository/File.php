<?php
namespace IManager\Repository;
use IManager\Repository\Database;

/**
 * Repositório de Arquivos presentes no banco de dados
 *
 * @version 1.0
 * @author Marcelo Gomes Martins
 */
class File
{

	public static $TABLE_NAME = '`implugin_file`';

	/**
	 * Verifica se determinado arquivo existe o banco de arquivos
	 * @param mixed $hash representação do arquivo em hash
	 * @return mixed resposta
	 */
	public static function exists($hash)
	{
		$database = Database::get_instance();
		try {
			$query = 'SELECT * FROM ' . self::$TABLE_NAME . ' WHERE `hash` = :hash;';
			$result = $database->execute_query($query, [
				':hash' => $hash
			]);
			return (isset($result[0]->id)) ? $result[0]->id : false;
		}
		catch(\Exception $e)
		{
			return false;
		}
	}

	/**
	 * Procura imagem por id
	 * @param mixed $id id da imagem
	 * @return mixed
	 */
	public static function find($id)
	{
		$database = Database::get_instance();
		try {
			$query = 'SELECT * FROM ' . self::$TABLE_NAME . ' WHERE `id` = :id;';
			$result = $database->execute_query($query, [
				':id' => $id
			]);
			return (isset($result[0])) ? $result[0] : false;
		}
		catch(\Exception $e)
		{
			return false;
		}
	}

	/**
	 * Salva arquivo em banco de dados
	 *
	 * @param mixed $type extensão do arquivo
	 * @param mixed $hash hash representativa do arquivo
	 * @param mixed $size tamanho do arquivo em
	 * @param mixed $width largura da imagem salva
	 * @param mixed $height altura da imagem salva
	 * @return \array|boolean resposta
	 */
	public static function save($type, $hash, $size, $width, $height)
	{
		$database = Database::get_instance();
		try {
			$query = 'INSERT INTO ' . self::$TABLE_NAME . ' (`type`, `hash`, `size`, `width`, `height`) ' .
			'VALUES (:type, :hash, :size, :width, :height)';
			$result = $database->execute_update($query, [
				':type' => $type,
				':hash' => $hash,
				':size' => $size,
				':width' => $width,
				':height' => $height
			]);
			return $result;
		}
		catch(\Exception $e)
		{
			return false;
		}
	}

	/**
	 * Exclui imagem do banco de dados
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

}