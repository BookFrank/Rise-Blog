<?php
namespace Stylite\Controller;

class Controller{

	protected $pageData;

	/**
	 * [渲染模板]
	 * @param  [string] $name   [模板名]
	 * @return [type]    html   [html页面]
	 */
	public function render($name)
	{
		$themePath = ROOT.'/app/View/'.env('theme');
		$loader = new \Twig_Loader_Filesystem($themePath);
		$twig = new \Twig_Environment($loader);
		return $twig->render($name.'.html', $this->pageData);
	}

	/**
	 * [页面填充数据方法]
	 * @param  [string] $key   [key]
	 * @param  [type]   $value [description]
	 * @return [type]        [description]
	 */
	public function assign($key, $value)
	{
		$this->pageData[$key] = $value;
	}

	/**
	 * [载入静态化文件]
	 * @param  [type] $type [blog page tags ]
	 * @param  [type] $name [filename]
	 * @return [string]     [html页面]
	 */
	public function loadStatic($type, $name, $pageNum=0)
	{
		if ($type == "page" || $type == "blog") {
			$file = ROOT.'/public/static/'.$type.'/'.$name.".html";
			if (file_exists($file)) {
				echo file_get_contents($file);
				exit();
			}
		}else{
			$file = ROOT.'/public/static/'.$type.'/'.$name.'_'.$pageNum.".html";
			if (file_exists($file)) {
				echo file_get_contents($file);
				exit();
			}
		}
	}

	/**
	 * [保存静态文件]
	 * @param  [type] $type [控制器名称]
	 * @param  [type] $name [静态化文件名]
	 * @return 
	 */
	public function saveStatic($type, $name, $content, $pageNum=0)
	{
		if ($type == "page" || $type == "blog") {
			$file = ROOT.'/public/static/'.$type.'/'.$name.".html";
		}else{
			$file = ROOT.'/public/static/'.$type.'/'.$name.'_'.$pageNum.".html";
		}
		$fp = fopen($file, "w") or die("unable to open file");
		fwrite($fp, $content);
		fclose($fp);
	}



}