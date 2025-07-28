# nexophp


nexophp.netlify.app

- 安装

~~~
composer install  --ignore-platform-reqs
~~~

- 配置重写

WEB目录指向`public`

~~~
location / {
  if (!-e $request_filename){
    rewrite ^(.*)$ /index.php last;
  }
}
~~~
 
# dev

~~~
composer update nexophp/* --ignore-platform-reqs -vvv
~~~

