<?php
namespace Stylite\Model;

class BlogModel extends Model{

	private $blogs = []; // 所有blog

	private $tags = []; // 所有tags

	private $categories = []; // 所有category

	private $yearMonths = []; // 所有日期 date

	private $cateBlogs; // cateID & blogId对应关系

	private $tagBlogs; // tagId & blogId对应关系

	private $dateBlogs; // date & blogId对应关系

	public function getAllBlogs(){ return $this->blogs; }
	public function getAllCategories(){ return $this->categories; }
	public function getAllTags(){ return $this->tags; }
	public function getAllDates(){ return $this->yearMonths; }

	public function __construct()
	{
		$blogPath = ROOT."/blog";
		$allFiles = get_file_in_dir($blogPath);
		$this->readAllBlogs($allFiles);
		// print_r($this->yearMonths);
		$this->blogs = multi_array_sort($this->blogs, 'date');
		$this->yearMonths = multi_array_sort($this->yearMonths, 'name');
		// print_r($this->yearMonths);exit();
	}

	/********************************博客初始化 START********************************/

	/**
	 * [实例化模型时读取所有md文件]
	 * @param  [type] $files [description]
	 * @return [type]        [description]
	 */
	public function readAllBlogs($files)
	{
		foreach ($files as $file) {
			$blogs = [];
			$fileCont = file_get_contents($file['path']);
			$fileName = urlencode(substr($file['name'], 0, -3));
			$siteURL = '/blog/'.$fileName.'.html';
			$blogId = md5($siteURL);
			$blog = [
				"blogId" => $blogId,
				"fileName" => $file['name'],
				"serverPath" => $file['path'],
				"mtime" => date('Y-m-d h:i:s', $file['mdate']),
				"ctime" => date('Y-m-d h:i:s', $file['cdate']),
				"siteURL" => $siteURL,
				"content" => (string)$this->parseMarkdown($fileCont)
			];

			$pattern = '/<\!\-\-(.*?)\-\->/is';
			preg_match($pattern, $fileCont, $matches);
			$metaInfo = trim($matches[1]);
			$metaInfoArr = explode("\n", $metaInfo);
			$blogMetaInfo = $this->readBlogMetaInfo($metaInfoArr, $blogId);
			$blog = array_merge($blog, $blogMetaInfo);
			$this->blogs[$blogId] = $blog;
		}
	}

	/**
	 * [读取博客的元信息]
	 * @param  [type] $metaInfoArr [description]
	 * @param  [type] $metaInfoArr [description]
	 * @return [type]              [description]
	 */
	public function readBlogMetaInfo($metaInfoArr, $blogId)
	{
		$meta = [];
		foreach ($metaInfoArr as $oneMeta) 
		{
			$attr = trim(strstr($oneMeta, ":", true));
			$val =  trim(strstr($oneMeta, ":"));
			$val = substr($val, 1); // 去除:

			switch ($attr) {
				case 'date':
					$ts = strtotime($val);
					$date = [
						'id' => date('Ym', $ts),
						'name' => date('Y-m', $ts),
						'url' => '/archive/'.date('Ym', $ts).'.html'
					];
					if (!array_key_exists($date['id'], $this->yearMonths)) {
						$this->yearMonths[$date['id']] = $date;
					}
					$meta['date'] = date('Y-m-d', $ts); 
					$meta['timestamp'] = $ts;
					$this->dateBlogs[$date['id']][] = $blogId;
					break;
				case 'tags':
					$tagArr = $this->spiltKeywords2Arr($val);
					foreach ($tagArr as $oneTag) {
						$tagId = abs(crc32(md5($oneTag)));
						$tag = [
							'id' => $tagId,
							'name' => $oneTag,
							'url' => '/tags/'.$tagId.'.html'
						];
						if (!array_key_exists($tagId, $this->tags)) {
							$this->tags[$tagId] = $tag;
						}
						$meta['tags'][] = $tag;
						$this->tagBlogs[$tagId][] = $blogId;
					}
					break;
				case 'category':
					$cateArr = $this->spiltKeywords2Arr($val);
					foreach ($cateArr as $oneCate) {
						$cateId = abs(crc32(md5($oneCate)));
						$cate = [
							'id' => $cateId,
							'name' => $oneCate,
							'url' => '/category/'.$cateId.'.html'
						];
						if (!array_key_exists($cateId, $this->categories)) {
							$this->categories[$cateId] = $cate;
						}
						$meta['category'][] = $cate;
						$this->cateBlogs[$cateId][] = $blogId;
					}
					break;
				case 'images':
					$meta['images'] = $this->spiltKeywords2Arr($val);
					break;
				default:
					$meta[$attr] = $val;
					break;
			}
		}
		return $meta;
	}

	/**
	 * [用户输入的多段字符串分组]
	 * @param  [type] $keywordsStr [description]
	 * @return [type]              [description]
	 */
	private function spiltKeywords2Arr($keywordsStr) 
	{
		$keywordArr = [];
		mb_regex_encoding("UTF-8");
		mb_internal_encoding("UTF-8");
		$tagArrTmp = mb_split("[\s,;|，；、]+", $keywordsStr);
		foreach ($tagArrTmp as $keyword) {
			$keyword = trim($keyword);
			if ($keyword != "" && !in_array($keyword, $keywordArr)) { $keywordArr[] = $keyword; }
		}
		return $keywordArr;
	}

	/**
	 * [解析markdown文件内容为html文本]
	 * @param  [type] $text [description]
	 * @return [type]       [description]
	 */
	private function parseMarkdown($text) 
	{
		return \Parsedown::instance()->parse($text);
	}

	/********************************博客初始化 END********************************/


	/********************************Page START********************************/

	/**
	 * [获取博客总页数]
	 * @param  [type] $pageSize [description]
	 * @return [type]           [description]
	 */
	public function getTotalPagesByPage($pageSize)
	{
		$count = count($this->blogs);
		return ceil($count/$pageSize);
	}

	/**
	 * [分页获取首页博客]
	 * @param  [type] $pageNum  [description]
	 * @param  [type] $pageSize [description]
	 * @return [type]           [description]
	 */
	public function getBlogsByPage($pageNum, $pageSize)
	{
		$offset = ($pageNum - 1) * $pageSize;
		$blogsList = array_slice($this->blogs, $offset, $pageSize);
		return $blogsList;
	}

	/**
	 * [根据分类id获取分类详情]
	 * @param  [type] $cateId [description]
	 * @return [type]         [description]
	 */
	public function getCategoryByCid($cateId)
	{
		return $this->categories[$cateId];
	}

	/**
	 * [获取某分类下博客总页数]
	 * @param  [type] $cateId   [description]
	 * @param  [type] $pageSize [description]
	 * @return [type]           [description]
	 */
	public function getTotalPagesByCate($cateId, $pageSize)
	{
		$count = count($this->cateBlogs[$cateId]);
		return ceil($count/$pageSize);
	}

	/**
	 * [分页获取某分类下博客]
	 * @param  [type] $cateId   [description]
	 * @param  [type] $pageNum  [description]
	 * @param  [type] $pageSize [description]
	 * @return [type]           [description]
	 */
	public function getBlogsByCate($cateId, $pageNum, $pageSize)
	{
		$blogList = [];
		$blogIds = $this->cateBlogs[$cateId];
		foreach ($blogIds as $blogId) {
			$blogList[] = $this->blogs[$blogId];
		}
		$blogList = multi_array_sort($blogList, 'date');
		$offset = ($pageNum - 1) * $pageSize;
		$cateBlogList = array_slice($blogList, $offset, $pageSize);
		return $cateBlogList;
	}

	/**
	 * [根据标签id获取标签详情]
	 * @param  [type] $tagId [description]
	 * @return [type]        [description]
	 */
	public function getTagByTagId($tagId)
	{
		return $this->tags[$tagId];
	}

	/**
	 * [获取某标签下博客总页数]
	 * @param  [type] $tagId    [description]
	 * @param  [type] $pageSize [description]
	 * @return [type]           [description]
	 */
	public function getTotalPagesByTag($tagId, $pageSize)
	{
		$count = count($this->tagBlogs[$tagId]);
		return ceil($count/$pageSize);
	}

	/**
	 * [分页获取某标签下的博客]
	 * @param  [type] $tagId    [description]
	 * @param  [type] $pageNum  [description]
	 * @param  [type] $pageSize [description]
	 * @return [type]           [description]
	 */
	public function getBlogsByTag($tagId, $pageNum, $pageSize)
	{
		$blogList = [];
		$blogIds = $this->tagBlogs[$tagId];
		foreach ($blogIds as $blogId) {
			$blogList[] = $this->blogs[$blogId];
		}
		$blogList = multi_array_sort($blogList, 'date');
		$offset = ($pageNum - 1) * $pageSize;
		$tagBlogList = array_slice($blogList, $offset, $pageSize);
		return $tagBlogList;
	}

	/**
	 * [根据日期id获取日期详情]
	 * @param  [type] $dateId [description]
	 * @return [type]         [description]
	 */
	public function getDateByDateId($dateId)
	{
		return $this->yearMonths[$dateId];
	}

	/**
	 * [获取某日期下博客总页数]
	 * @param  [type] $dateId   [description]
	 * @param  [type] $pageSize [description]
	 * @return [type]           [description]
	 */
	public function getTotalPagesByDate($dateId, $pageSize)
	{
		$count = count($this->dateBlogs[$dateId]);
		return ceil($count/$pageSize);
	}

	/**
	 * [分页获取某日期归档下的博客]
	 * @param  [type] $dateId   [description]
	 * @param  [type] $pageNum  [description]
	 * @param  [type] $pageSize [description]
	 * @return [type]           [description]
	 */
	public function getBlogsByDate($dateId, $pageNum, $pageSize)
	{
		$blogList = [];
		$blogIds = $this->dateBlogs[$dateId];
		foreach ($blogIds as $blogId) {
			$blogList[] = $this->blogs[$blogId];
		}
		$blogList = multi_array_sort($blogList, 'date');
		$offset = ($pageNum - 1) * $pageSize;
		$dateBlogList = array_slice($blogList, $offset, $pageSize);
		return $dateBlogList;
	}

	/**
	 * [根据id获取博客]
	 * @param  [type] $blogId [description]
	 * @return [type]         [description]
	 */
	public function getBlogById($blogId)
	{
		return $this->blogs[$blogId];
	}

	/**
	 * [根据标题查找相关博客]
	 * @param  [type]  $title [description]
	 * @param  integer $max   [description]
	 * @return [type]         [description]
	 */
	public function getBlogByTitle($title, $max = 50) 
	{
		$blogList = [];
		foreach ($this->blogs as $blog) {
			$blogTitle = strtolower($blog['title']);
			$title = strtolower($title);
			if (strpos($blogTitle, $title) !== FALSE) { $blogList[] = $blog; }
			if (count($blogList) >= $max) break;
		}
		return $blogList;
	}
	/********************************Page END********************************/

}