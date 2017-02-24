<?php
namespace Stylite\Controller;
use Stylite\Model\BlogModel;

class BlogController extends Controller{


	private $blogModel;

	public function __construct()
	{
		$this->hasChange();
	}

	private function init()
	{
		$this->blogModel = new BlogModel();
		$allBlogsList = $this->blogModel->getAllBlogs();
		$categoryList = $this->blogModel->getAllCategories();
		$tagsList = $this->blogModel->getAllTags();
		$yearMonthList = $this->blogModel->getAllDates();

		$this->assign("allBlogsList", $allBlogsList);
		$this->assign("categoryList", array_values($categoryList));
		$this->assign("tagsList", array_values($tagsList));
		$this->assign("yearMonthList", array_values($yearMonthList));
		$this->assign("confObj", env());
		$this->assign("site", env());
	}

	/**
	 * [博客首页]
	 * @return [type] [description]
	 */
	public function index()
	{
		$this->page(1);
	}

	/**
	 * [首页分页展示]
	 * @param  [type] $pageNum [description]
	 * @return [type]          [description]
	 */
	public function page($pageNum)
	{
		$this->loadStatic("page", $pageNum);
		
		$this->init();
		$pageSize = env('pageSize');
		$pageBarSize = env('pageBarSize');

		$pageCount = $this->blogModel->getTotalPagesByPage($pageSize);
		$blogList = $this->blogModel->getBlogsByPage($pageNum, $pageSize);
		$pagination = $this->blogModel->splitPage($pageCount, $pageNum, $pageBarSize);
		
		$this->assign("pagination", $pagination);
		$this->assign("pageName", "home");
		$this->assign("pageNo", $pageNum);
		$this->assign("pages", $pageCount);
		$this->assign("blogList", $blogList);

		$html = $this->render('index');
		$this->saveStatic("page", $pageNum, $html);
		echo $html;
	}

	/**
	 * [分类文章展示]
	 * @param  [type]  $cateId  [description]
	 * @param  integer $pageNum [description]
	 * @return [type]           [description]
	 */
	public function category($cateId, $pageNum=1)
	{
		$this->loadStatic("category", $cateId, $pageNum);

		$this->init();
		$pageSize = env('pageSize');
		$pageBarSize = env('pageBarSize');

		$category = $this->blogModel->getCategoryByCid($cateId);
		$pageCount = $this->blogModel->getTotalPagesByCate($cateId, $pageSize);
		$blogList = $this->blogModel->getBlogsByCate($cateId, $pageNum, $pageSize);
		$pagination = $this->blogModel->splitPage($pageCount, $pageNum, $pageBarSize);

		$this->assign("category", $category);
		$this->assign("pagination", $pagination);
		$this->assign("pageName", "category");
		$this->assign("pageNo", $pageNum);
		$this->assign("pages", $pageCount);
		$this->assign("blogList", $blogList);

		$html = $this->render('index');
		$this->saveStatic("category", $cateId, $html, $pageNum);
		echo $html;
	}

	/**
	 * [日期归档文章展示]
	 * @param  [type]  $dateId  [description]
	 * @param  integer $pageNum [description]
	 * @return [type]           [description]
	 */
	public function archive($dateId, $pageNum=1)
	{
		$this->loadStatic("archive", $dateId, $pageNum);

		$this->init();
		$pageSize = env('pageSize');
		$pageBarSize = env('pageBarSize');

		$date = $this->blogModel->getDateByDateId($dateId);
		$pageCount = $this->blogModel->getTotalPagesByDate($dateId, $pageSize);
		$blogList = $this->blogModel->getBlogsByDate($dateId, $pageNum, $pageSize);
		$pagination = $this->blogModel->splitPage($pageCount, $pageNum, $pageBarSize, "/archive/$dateId/");

		$this->assign("yearMonthId", $dateId);
		$this->assign("yearMonth", $date);
		$this->assign("pagination", $pagination);
		$this->assign("pageName", "archive");
		$this->assign("pageNo", $pageNum);
		$this->assign("pages", $pageCount);
		$this->assign("blogList", $blogList);

		$html = $this->render('index');
		$this->saveStatic("archive", $dateId, $html, $pageNum);
		echo $html;
	}

	/**
	 * [标签文章展示]
	 * @param  [int]  $tagId   [description]
	 * @param  integer $pageNum [description]
	 * @return [type]           [description]
	 */
	public function tags($tagId, $pageNum=1)
	{
		$this->loadStatic("tags", $tagId, $pageNum);

		$this->init();
		$pageSize = env('pageSize');
		$pageBarSize = env('pageBarSize');

		$tag = $this->blogModel->getTagByTagId($tagId);
		$pageCount = $this->blogModel->getTotalPagesByTag($tagId, $pageSize);
		$blogList = $this->blogModel->getBlogsByTag($tagId, $pageNum, $pageSize);
		$pagination = $this->blogModel->splitPage($pageCount, $pageNum, $pageBarSize, "/tags/$tagId/");
		
		$this->assign("tag", $tag);
		$this->assign("pagination", $pagination);
		$this->assign("pageName", "tags");
		$this->assign("pageNo", $pageNum);
		$this->assign("pages", $pageCount);
		$this->assign("blogList", $blogList);

		$html = $this->render('index');
		$this->saveStatic("tags", $tagId, $html, $pageNum);
		echo $html;
	}

	/**
	 * [blog文章展示]
	 * @param  [string] $blogName [文档名称]
	 * @return  html页面
	 */
	public function blog($blogName) 
	{
		$this->loadStatic("blog", $blogName);

		$this->init();
		$openPage = "/blog/".$blogName.".html";
		$blogId = md5($openPage);
		
		$blog = $this->blogModel->getBlogById($blogId);

		$this->assign("pageName", "blog");
		$this->assign("blog", $blog);

		$html = $this->render('detail');
		$this->saveStatic("blog", $blogName);
		echo $html;
	}

	/**
	 * [文档检索方法]
	 * @return [type] [description]
	 */
	public function search() 
	{
		$keyword = trim($_GET['keyword']);
		$blogList = [];
		
		$this->init();
		if (!empty($keyword)) {
			$blogList = $this->blogModel->getBlogByTitle($keyword);
		}
		
		$this->assign("pageName", "search");
		$this->assign("keyword", $keyword);
		$this->assign("blogList", $blogList);
		
		$html = $this->render('index');
		echo $html;
	}

	/**
	 * [对比缓存判断有无任何变更]
	 * @return boolean [description]
	 */
	public function hasChange()
	{
		$cacheFile = ROOT.'/public/static/cache';
		if (!file_exists($cacheFile)) {
			$fr = fopen($cacheFile, "x");
			fclose($fr);
		}
		$cacheStr = file_get_contents($cacheFile);
		$cache = explode("\n", $cacheStr);

		$now = [];
		$blogPath = ROOT."/blog";
		$allFiles = get_file_in_dir($blogPath);
		foreach ($allFiles as $file) { $now[] = md5_file($file['path']);}
		$now[] = md5_file(ROOT.'/app/Config/config.php'); // 监控配置文件变更
		$diff = array_diff($cache, $now);
		if (count($diff) != 0) {
			$this->removeCacheHtml();
			$cont = implode("\n", $now);
			file_put_contents($cacheFile, $cont);
		}
	}

	/**
	 * [删除所有静态化的文件]
	 * @return [type] [description]
	 */
	public function removeCacheHtml()
	{
		$staticPath = ROOT.'/public/static';
		$allFile = get_file_in_dir($staticPath, "html");
		foreach ($allFile as $file) {
			unlink($file['path']);
		}
	}
}