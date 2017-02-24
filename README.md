# Stylite Blog 修行日志
> 人的一生就是一场修行，
>
> 修行的路上欢迎你使用Stylite Blog来记录你的旅程。

## 一. 简介 ##

Stylite Blog，中文名为修行日志，是一款使用PHP开发的支持Markdown的博客系统，特点就是非常简单，不需要数据库，也没有后台。本地编写完Markdown文件，上传至blog文件夹内，刷新页面就可以看到响应式的HTML页面。

## 二. 为什么写Stylite

自从认识了Markdown以后，便一直用喜欢用Markdown来写文章和技术文档。就是喜欢这种简单的形式，个人博客也由复杂的Wordpress转为简单的Gitblog来作为自己线上博客的系统。

在使用Gitblog过程中，发现Gitblog在一些方面不能满足我的要求，于是开始看Gitblog的代码准备改造一下，后来有段时间不是很忙就想着干脆我自己重写一个算了，于是先有了Viaduct路由，再一步一步地完成了Stylite。 

## 二. 功能特点 ##

1. Markdown
2. 支持静态化
3. 响应式页面
4. blog文件夹嵌套
5. 多说评论
6. 多主题切换

## 四. 环境要求 ##

PHP 5.5.9+

## 五. 安装说明 ##

1. - 使用composer安装或者直接下载源码  

     ```shell
     composer require bookfrank/stylite-blog
     ```


   - 先`clone`源码，然后执行`composer install`安装

     ```shell
     git clone https://github.com/BookFrank/Stylite-Blog.git
     composer install
     ```

2. Nginx配置虚拟主机指向`public`文件夹

   ```nginx
   server {
       listen      80;
       server_name your-blog.com;

       root  /path/to/project/public;
       location / {
          index index.php;
          try_files $uri $uri/ /index.php?$query_string;
       }

       #proxy the php scripts to php-fpm
       location ~ \.php$ {
           index index.php index.html;
           include /usr/local/etc/nginx/fastcgi.conf;
           fastcgi_intercept_errors on;
           fastcgi_pass 127.0.0.1:9000;
       }
   }
   ```

3. 给`public`文件夹授予权限，

   ```shell
   sudo chmod -R 777 public/ 
   ```

   然后打开浏览器，访问您的网站首页，

   www.your-blog.com

4. 上传Markdown文件到`blog`文件夹

   支持在`blog`文件夹内新建子文件夹  

## 六. 感谢 ##

如果你有幸看到这里，欢迎您来使用Stylite Blog来记录你的生活。

也非常欢迎根据你们的需要去随意改写代码。

感谢你们使用Stylite Blog。

Author: 李扬 Frank   

Email: bookfrank@foxmail.com