<?php

	/**
	 * [获取和设置配置 支持数组定义]
	 * @param  [type] $name    [description]
	 * @param  [type] $value   [description]
	 * @param  [type] $default [description]
	 * @return [type]          [description]
	 */
	function env($name=null, $value=null,$default=null)
	{
		static $_env = [];
		if (empty($name)) { return $_env; }

		if (is_string($name)) {
			$name = strtolower($name);
			// 判断是设置还是读取配置
			if (!is_null($value)) {
				$_env[$name] = $value;
				return null;
			}else{
				return isset($_env[$name]) ? $_env[$name] : null;
			}
		}

		// 初始化批量设置
		if (is_array($name)) { 
			$_env = array_merge($_env, array_change_key_case($name,CASE_LOWER)); 
		}
		return null;
	}

	/**
	 * [读取目录中的所有文件]
	 * @param  string $dir [目录地址]
	 * @return Array  $res [文件地址数组]
	 */
	function get_file_in_dir($dir="", $ext="md")
	{
		$res = [];
		if (!is_dir($dir)) { return [];}
		$files = scandir($dir);

		foreach ($files as $file) {
			$fullName = $dir.'/'.$file;
			$fileExt = pathinfo($fullName, PATHINFO_EXTENSION);
			if (is_dir($fullName)) {
				if ($file != "." && $file != "..") { $res = array_merge($res, get_file_in_dir($fullName, $ext)); }
			}else{
				if ($fileExt == $ext) { $res[] = getFileInfo($fullName); }
			}
		}
		return $res;	
	}


	/**
	 * [获取文件的属性信息]
	 * @param  [string] $filePath [文件地址]
	 * @return [Array]  $fileInfo [属性数组]
	 */
	function getFileInfo($file)
	{
		$fileInfo = [];
		$fileInfo['name'] = basename($file);
		$fileInfo['path'] = $file;
		$fileInfo['size'] = filesize($file);
		$fileInfo['mdate'] = filemtime($file); // 文件的内容上次被修改的时间
		$fileInfo['adate'] = fileatime($file); // 文件的内容上次被访问的时间
		$fileInfo['cdate'] = filectime($file);  //  取得文件的 inode 修改时间
		return $fileInfo;
	}


	/**
	 * [将关键词字符串转为数组，兼容中文与特殊符号]
	 * @param  [string] $str [description]
	 * @return [Array]      [description]
	 */
	function mbSpiltStr($str) {
		$strArr = [];
		mb_regex_encoding("UTF-8");
		mb_internal_encoding("UTF-8");
		$strArrTmp = mb_split("[\s,;|，；、]+", $str);
		foreach ($strArrTmp as $word) {
			$word = trim($word);
			if ($word != "" && !in_array($word, $strArr)) { $strArr[] = $word; }
		}
		return $strArr;
	}

	/**
	 * [多维数组根据某一列排序]
	 * @return [type] [description]
	 */
	function multi_array_sort($sortArr, $sortKey, $sort = SORT_DESC)
	{
		$keyArr = [];
		if (is_array($sortArr)) {
			if (count($sortArr == 1)) {
				return $sortArr;
			}
			foreach ($sortArr as $secArr) {
				$keyArr[] = $secArr[$sortKey];
			}
		}
		array_multisort($keyArr, $sort, $sortArr);
		return $sortArr;
	}

	/**
	 * [求出两个数组的不一致项]
	 * @return [Array] [description]
	 */
	function mutual_array_diff($arrA, $arrB)
	{
		$res = [];
		if (is_array($arrA) && is_array($arrB)) {
			$diffA = array_diff($arrA, $arrB);
			$diffB = array_diff($arrB, $arrA);
			$res = array_merge($res, $diffA, $diffB);
		}
		return $res;
	}

