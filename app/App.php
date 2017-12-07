<?php
namespace Stylite;

class App{

	public static $instance;

	private function __construct(){}

	public static function getInstance()
	{
		if (!is_object(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * [开启应用]
	 *
	 * @return [type] [description]
	 */
	public function start()
	{
		$this->loadConfig();
	}

	/**
	 * [加载配置]
	 *
	 * @return [type] [description]
	 */
	public function loadConfig()
	{
		$config = include_once ROOT."/app/Config/config.php";
		env($config);
	}

}