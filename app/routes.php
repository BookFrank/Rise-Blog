<?php
use Bookfrank\Viaduct\Router;

/**************************************** Rise Routes Start ******************************************/
Router::get("/", "Stylite\Controller\BlogController@index");

Router::get("page/{pageNum}.html", "Stylite\Controller\BlogController@page");

Router::get("blog/{blogName}.html", "Stylite\Controller\BlogController@blog");

Router::get("search", "Stylite\Controller\BlogController@search");

Router::get("category/{cateId}.html", "Stylite\Controller\BlogController@category");
Router::get("category/{cateId}/page/{pageNum}.html", "Stylite\Controller\BlogController@category");

Router::get("tags/{tagId}.html", "Stylite\Controller\BlogController@tags");
Router::get("tags/{tagId}/page/{pageNum}.html", "Stylite\Controller\BlogController@tags");

Router::get("archive/{dateId}.html", "Stylite\Controller\BlogController@archive");
Router::get("archive/{dateId}/page/{pageNum}.html", "Stylite\Controller\BlogController@archive");

Router::get("hello", function(){
	echo env('theme');
});
/**************************************** Rise Routes End ******************************************/



/**************************************** User Routes Start ******************************************/

/**************************************** User Routes End ******************************************/

Router::dispatch();