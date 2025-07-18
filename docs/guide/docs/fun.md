# PHP 实用函数手册

本手册提供了一组 PHP 实用函数的概览，这些函数旨在简化常见任务，如 Redis 操作、文件处理、字符串操作、Web 工具、数据转换等。每个函数都记录了其用途、参数和示例用法。

## 目录

1. [Redis 操作](#redis-操作)
2. [文件处理](#文件处理)
3. [字符串操作](#字符串操作)
4. [Web 工具](#web-工具)
5. [数据转换](#数据转换)
6. [其他功能](#其他功能)

---

## Redis 操作

### `predis()`
使用 Predis 库连接到 Redis 服务器并返回客户端实例。

**参数：**
- 无（使用全局 `$redis_config` 获取主机、端口和认证信息）。

**返回值：**
- Predis\Client：Redis 客户端实例。

**示例：**
```php
$redis = predis();
$redis->set('key', 'value');
echo $redis->get('key'); // 输出：value
```

### `predis_add_geo($key, $arr)`
将地理位置数据添加到 Redis 排序集合。

**参数：**
- `$key` (string)：Redis 排序集合的键。
- `$arr` (array)：包含经度 (`lng`)、纬度 (`lat`) 和标题 (`title`) 的位置数组。

**示例：**
```php
predis_add_geo('places', [
    [
        'lng' => '116.397128',
        'lat' => '39.916527',
        'title' => '北京天安门'
    ]
]);
```

### `predis_delete_geo($key, $arr)`
从 Redis 排序集合中删除指定地理位置。

**参数：**
- `$key` (string)：Redis 排序集合的键。
- `$arr` (array)：要删除的标题数组。

**示例：**
```php
predis_delete_geo('places', ['北京天安门']);
```

### `predis_get_pager($key, $lat, $lng, $juli = 2, $sort = 'ASC', $to_fixed = 2)`
返回指定坐标附近的地点的分页数据。

**参数：**
- `$key` (string)：Redis 排序集合的键。
- `$lat` (float)：纬度。
- `$lng` (float)：经度。
- `$juli` (float)：搜索半径（默认 2 公里）。
- `$sort` (string)：排序方式（默认 'ASC'）。
- `$to_fixed` (int)：距离保留小数位数（默认 2）。

**返回值：**
- array：包含分页信息的数组（`current_page`, `data`, `last_page`, `per_page`, `total`, `total_cur`）。

**示例：**
```php
$result = predis_get_pager('places', 39.915049, 116.403958);
pr($result); // 输出附近地点的分页数据
```

### `predis_geo_pos($key, $title = [], $to_fixed = 6)`
获取指定地点的经纬度。

**参数：**
- `$key` (string)：Redis 排序集合的键。
- `$title` (array)：要查询的标题数组。
- `$to_fixed` (int)：经纬度保留小数位数（默认 6）。

**返回值：**
- array：包含标题和对应经纬度的数组。

**示例：**
```php
$positions = predis_geo_pos('places', ['北京天安门']);
pr($positions); // 输出经纬度数据
```

### `redis_pub($channel, $message)`
发布消息到指定 Redis 频道。

**参数：**
- `$channel` (string)：Redis 频道名称。
- `$message` (mixed)：要发布的消息（数组会自动转为 JSON）。

**示例：**
```php
redis_pub('demo', 'welcome man');
// 或
redis_pub('demo', ['title' => 'yourname']);
```

### `redis_sub($channel, $call, $unsubscribe = false)`
订阅 Redis 频道并处理接收到的消息。

**参数：**
- `$channel` (string)：Redis 频道名称。
- `$call` (callable)：处理消息的回调函数。
- `$unsubscribe` (bool)：是否在接收消息后取消订阅（默认 false）。

**示例：**
```php
redis_sub('demo', function($channel, $message) {
    echo "channel $channel\n";
    print_r($message);
});
```

### `cache($key, $data = '', $second = null)`
设置或获取 Redis 缓存。

**参数：**
- `$key` (string)：缓存键。
- `$data` (mixed)：要存储的数据（为空时获取缓存，null 时删除缓存）。
- `$second` (int|null)：缓存过期时间（秒）。

**返回值：**
- mixed：缓存数据或 null。

**示例：**
```php
cache('my_key', 'my_value', 3600); // 设置缓存
echo cache('my_key'); // 获取缓存
cache('my_key', null); // 删除缓存
```

### `cache_delete($key)`
删除指定 Redis 缓存。

**参数：**
- `$key` (string)：缓存键。

**示例：**
```php
cache_delete('my_key');
```

### `lock_call($key, $call, $time = 10)`
基于 Redis 的锁机制执行回调函数。

**参数：**
- `$key` (string)：锁的键。
- `$call` (callable)：要执行的回调函数。
- `$time` (int)：锁的超时时间（秒，默认 10）。

**示例：**
```php
lock_call('my_lock', function() {
    echo 'Locked operation';
}, 5);
```

### `lock_start($key, $time = 1)`
开始 Redis 锁。

**参数：**
- `$key` (string)：锁的键。
- `$time` (int)：锁的超时时间（秒，默认 1）。

**返回值：**
- bool：锁是否成功。

**示例：**
```php
if (lock_start('my_lock')) {
    // 执行操作
    lock_end();
}
```

### `lock_end()`
释放 Redis 锁。

**示例：**
```php
lock_end();
```

---

## 文件处理

### `_download_file($url, $contain_http = false)`
下载文件并返回本地路径。

**参数：**
- `$url` (string)：文件 URL。
- `$contain_http` (bool)：是否返回包含完整 HTTP 地址的路径（默认 false）。

**返回值：**
- string：文件本地路径。

**示例：**
```php
$path = _download_file('https://example.com/image.jpg');
echo $path; // 输出本地路径
```

### `download_file($url, $mimes = ['image/*', 'video/*'], $cons = [], $contain_http = false)`
下载指定类型的资源文件到本地。

**参数：**
- `$url` (string)：文件 URL。
- `$mimes` (array)：允许的 MIME 类型（默认图片和视频）。
- `$cons` (array)：URL 必须包含的字符串。
- `$contain_http` (bool)：是否返回完整 HTTP 路径（默认 false）。

**返回值：**
- string|null：文件本地路径或 null。

**示例：**
```php
$path = download_file('https://example.com/image.jpg', ['image/*']);
echo $path; // 输出本地路径
```

### `download_remote_file($url, $path = '')`
下载远程文件到本地并返回 CDN 路径。

**参数：**
- `$url` (string)：远程文件 URL。
- `$path` (string)：本地存储路径（默认使用全局 `$remote_to_local_path`）。

**返回值：**
- string：CDN 路径。

**示例：**
```php
$cdn_url = download_remote_file('https://example.com/file.pdf');
echo $cdn_url; // 输出 CDN 路径
```

### `load_xls($new_arr = [])`
加载 XLSX 文件并解析为数组。

**参数：**
- `$new_arr` (array)：
  - `file` (string)：XLSX 文件路径。
  - `config` (array)：列名映射。
  - `title_line` (int)：标题行号（默认 1）。
  - `call` (callable)：每单元格回调函数。
  - `is_full` (bool)：是否返回完整数据（默认 false）。

**返回值：**
- array：解析后的数据或完整数据结构。

**示例：**
```php
$data = load_xls([
    'file' => 'example.xlsx',
    'config' => ['序号' => 'index'],
    'title_line' => 1,
    'call' => function($i, $row, &$d) {}
]);
pr($data); // 输出解析后的数据
```

### `csv_reader($file)`
读取 CSV 文件内容。

**参数：**
- `$file` (string)：CSV 文件路径。

**返回值：**
- array：CSV 数据。

**示例：**
```php
$data = csv_reader('data.csv');
pr($data); // 输出 CSV 数据
```

### `csv_writer($file, $header = [], $content = [])`
写入数据到 CSV 文件。

**参数：**
- `$file` (string)：CSV 文件路径。
- `$header` (array)：表头数组。
- `$content` (array)：内容数组。

**返回值：**
- bool：写入是否成功。

**示例：**
```php
csv_writer('output.csv', ['ID', 'Name'], [['1', 'John'], ['2', 'Jane']]);
```

### `zip_extract($local_file, $extract_local_dir)`
解压本地 ZIP 文件到指定目录。

**参数：**
- `$local_file` (string)：ZIP 文件路径。
- `$extract_local_dir` (string)：解压目标目录。

**返回值：**
- bool：解压是否成功。

**示例：**
```php
zip_extract('archive.zip', '/path/to/extract');
```

### `zip_create($local_zip_file, $files = [])`
创建 ZIP 文件。

**参数：**
- `$local_zip_file` (string)：ZIP 文件路径。
- `$files` (array)：要压缩的文件数组。

**返回值：**
- string：创建的 ZIP 文件路径（相对于 PATH）。

**示例：**
```php
$zip_path = zip_create('/path/to/archive.zip', ['file1.txt', 'file2.txt']);
echo $zip_path; // 输出 ZIP 文件路径
```

### `get_include_content($local_file)`
获取本地 PHP 文件的输出内容。

**参数：**
- `$local_file` (string)：PHP 文件路径。

**返回值：**
- string|null：文件输出内容或 null。

**示例：**
```php
$content = get_include_content('template.php');
echo $content; // 输出文件内容
```

---

## 字符串操作

### `gbk_substr($text, $start, $len, $gbk = 'GBK')`
按 GBK 编码截取字符串（一个中文算 2 个字符）。

**参数：**
- `$text` (string)：输入字符串。
- `$start` (int)：起始位置。
- `$len` (int)：截取长度。
- `$gbk` (string)：编码（默认 'GBK'）。

**返回值：**
- string：截取后的字符串。

**示例：**
```php
echo gbk_substr('你好世界', 0, 4); // 输出：你好
```

### `get_gbk_len($value, $gbk = 'GBK')`
计算字符串的 GBK 编码长度。

**参数：**
- `$value` (string)：输入字符串。
- `$gbk` (string)：编码（默认 'GBK'）。

**返回值：**
- int：字符串长度。

**示例：**
```php
echo get_gbk_len('你好'); // 输出：4
```

### `get_text_center($str, $len)`
将字符串居中对齐。

**参数：**
- `$str` (string)：输入字符串。
- `$len` (int)：总长度。

**返回值：**
- string：居中对齐的字符串。

**示例：**
```php
echo get_text_center('你好', 8); // 输出：  你好  
```

### `get_text_left_right($arr, $length, $return_arr = false)`
将字符串数组按左中右排版。

**参数：**
- `$arr` (array)：字符串数组。
- `$length` (int)：总长度。
- `$return_arr` (bool)：是否返回数组（默认 false）。

**返回值：**
- string|array：排版后的字符串或数组。

**示例：**
```php
echo get_text_left_right(['左', '右'], 10); // 输出：左    右
```

### `string_to_array($name, $array = '')`
将字符串按分隔符转为数组。

**参数：**
- `$name` (string)：输入字符串。
- `$array` (array)：分隔符数组（默认包含换行、逗号等）。

**返回值：**
- array：分割后的数组。

**示例：**
```php
pr(string_to_array('a,b,c')); // 输出：[a, b, c]
```

### `text_add_br($text, $w, $br = '<br>')`
为长文本自动添加换行。

**参数：**
- `$text` (string)：输入文本。
- `$w` (int)：每行最大长度。
- `$br` (string)：换行符（默认 '<br>'）。

**返回值：**
- string：添加换行后的文本。

**示例：**
```php
echo text_add_br('这是一段很长的文本', 4); // 输出：这段<br>很长<br>的文<br>本
```

### `get_str_number($input)`
提取字符串中的数字。

**参数：**
- `$input` (string)：输入字符串。

**返回值：**
- array：提取的数字数组。

**示例：**
```php
pr(get_str_number('Price: 12.34 USD')); // 输出：[12.34]
```

---

## Web 工具

### `allow_cross_origin()`
设置跨域请求头。

**示例：**
```php
allow_cross_origin(); // 设置跨域头
```

### `online_view_office($url)`
生成在线查看 Office 文件的 URL。

**参数：**
- `$url` (string)：Office 文件 URL。

**返回值：**
- string：在线查看 URL。

**示例：**
```php
echo online_view_office('https://example.com/doc.docx'); // 输出 Office 在线查看链接
```

### `jump($url)`
页面重定向。

**参数：**
- `$url` (string)：目标 URL。

**示例：**
```php
jump('/home'); // 重定向到 /home
```

### `cdn()`
获取 CDN 地址。

**返回值：**
- string：CDN 地址。

**示例：**
```php
echo cdn(); // 输出 CDN 地址
```

### `json($data)`
以 JSON 格式输出数据并退出。

**参数：**
- `$data` (mixed)：要输出的数据。

**示例：**
```php
json(['code' => 0, 'msg' => 'success']); // 输出 JSON 数据
```

### `json_error($arr = [])`
输出错误 JSON 响应。

**参数：**
- `$arr` (array)：错误信息数组。

**返回值：**
- JSON 响应。

**示例：**
```php
json_error(['msg' => 'Invalid input']); // 输出错误 JSON
```

### `json_success($arr = [])`
输出成功 JSON 响应。

**参数：**
- `$arr` (array)：成功信息数组。

**返回值：**
- JSON 响应。

**示例：**
```php
json_success(['data' => 'ok']); // 输出成功 JSON
```

### `get_mime($url)`
获取文件的 MIME 类型。

**参数：**
- `$url` (string)：文件 URL 或路径。

**返回值：**
- string：MIME 类型。

**示例：**
```php
echo get_mime('https://example.com/image.jpg'); // 输出：image/jpeg
```

### `get_mime_content($content, $just_return_ext = false)`
从内容获取 MIME 类型。

**参数：**
- `$content` (string)：文件内容。
- `$just_return_ext` (bool)：是否仅返回扩展名（默认 false）。

**返回值：**
- string：MIME 类型或扩展名。

**示例：**
```php
$content = file_get_contents('image.jpg');
echo get_mime_content($content); // 输出：image/jpeg
```

### `get_remote_file($url, $is_json = false)`
获取远程文件内容。

**参数：**
- `$url` (string)：远程文件 URL。
- `$is_json` (bool)：是否解析为 JSON（默认 false）。

**返回值：**
- string|array：文件内容或 JSON 解析结果。

**示例：**
```php
$content = get_remote_file('https://api.example.com/data', true);
pr($content); // 输出 JSON 解析后的数组
```

### `is_json_request()`
判断是否为 JSON 请求。

**返回值：**
- bool：是否为 JSON 请求。

**示例：**
```php
if (is_json_request()) {
    echo 'This is a JSON request';
}
```

### `html_error($all)`
输出 HTML 错误页面。

**参数：**
- `$all` (array|string)：错误信息。

**返回值：**
- string：HTML 错误内容。

**示例：**
```php
echo html_error(['error' => 'Invalid input']); // 输出错误 HTML
```

### `add_js($code)`
添加 JavaScript 代码。

**参数：**
- `$code` (string)：JS 代码或文件路径。

**示例：**
```php
add_js('alert("Hello");');
```

### `render_js()`
输出内联 JavaScript 代码。

**示例：**
```php
render_js(); // 输出所有添加的 JS 代码
```

### `render_js_file()`
输出 JavaScript 文件引用。

**示例：**
```php
render_js_file(); // 输出所有 JS 文件的 <script> 标签
```

### `add_css($code)`
添加 CSS 代码。

**参数：**
- `$code` (string)：CSS 代码或文件路径。

**示例：**
```php
add_css('body { background: #fff; }');
```

### `render_css()`
输出内联 CSS 代码。

**示例：**
```php
render_css(); // 输出所有添加的 CSS 代码
```

### `render_css_file()`
输出 CSS 文件引用。

**示例：**
```php
render_css_file(); // 输出所有 CSS 文件的 <link> 标签
```

### `view($file, $params = [])`
渲染视图文件。

**参数：**
- `$file` (string)：视图文件路径（相对于模块/控制器）。
- `$params` (array)：传递给视图的变量。

**返回值：**
- string：渲染后的内容。

**示例：**
```php
echo view('index', ['title' => 'Home']);
```

### `get_reffer($refer = '')`
获取请求的 Referer。

**参数：**
- `$refer` (string)：Referer URL（默认使用 $_SERVER['HTTP_REFERER']）。

**返回值：**
- string：处理后的 Referer URL。

**示例：**
```php
echo get_reffer(); // 输出 Referer URL
```

### `get_root_domain($host = '')`
获取主域名。

**参数：**
- `$host` (string)：主机名（默认使用全局配置）。

**返回值：**
- string：主域名。

**示例：**
```php
echo get_root_domain('admin.baidu.com'); // 输出：baidu.com
```

### `get_sub_domain($host = '')`
获取子域名。

**参数：**
- `$host` (string)：主机名（默认使用全局配置）。

**返回值：**
- string：子域名。

**示例：**
```php
echo get_sub_domain('admin.baidu.com'); // 输出：admin
```

### `create_url($url)`
生成完整的 URL。

**参数：**
- `$url` (string)：相对或绝对 URL。

**返回值：**
- string：完整 URL。

**示例：**
```php
echo create_url('/home'); // 输出：https://example.com/home
```

### `block_start($name)`
开始捕获页面块内容。

**参数：**
- `$name` (string)：块名称。

**示例：**
```php
block_start('content');
```

### `block_end($is_muit = false)`
结束捕获页面块内容。

**参数：**
- `$is_muit` (bool)：是否允许多个同名块（默认 false）。

**返回值：**
- string：捕获的内容。

**示例：**
```php
block_end();
```

### `block_clean()`
清空所有页面块内容。

**示例：**
```php
block_clean();
```

### `get_block($name = '')`
获取指定页面块内容。

**参数：**
- `$name` (string)：块名称（为空时返回所有块）。

**返回值：**
- mixed：指定块内容或所有块内容。

**示例：**
```php
echo get_block('content'); // 输出指定块内容
```

---

## 数据转换

### `to_utf8($str)`
将字符串或数组转换为 UTF-8 编码。

**参数：**
- `$str` (mixed)：输入字符串或数组。

**返回值：**
- mixed：转换后的数据。

**示例：**
```php
echo to_utf8('你好'); // 输出：你好
```

### `array_to_object($arr)`
将数组转换为对象。

**参数：**
- `$arr` (array)：输入数组。

**返回值：**
- object：转换后的对象。

**示例：**
```php
$obj = array_to_object(['name' => 'John']);
print_r($obj); // 输出对象
```

### `object_to_array($obj)`
将对象转换为数组。

**参数：**
- `$obj` (object)：输入对象。

**返回值：**
- array：转换后的数组。

**示例：**
```php
$arr = object_to_array((object)['name' => 'John']);
pr($arr); // 输出数组
```

### `array_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'children', $root = 0, $my_id = '')`
将平面数组转换为树形结构。

**参数：**
- `$list` (array)：输入数组。
- `$pk` (string)：主键字段（默认 'id'）。
- `$pid` (string)：父 ID 字段（默认 'pid'）。
- `$child` (string)：子节点字段（默认 'children'）。
- `$root` (int)：根节点 ID（默认 0）。
- `$my_id` (string)：当前 ID（可选）。

**返回值：**
- array：树形结构数组。

**示例：**
```php
$list = [
    ['id' => 1, 'pid' => 0, 'name' => 'Parent'],
    ['id' => 2, 'pid' => 1, 'name' => 'Child']
];
$tree = array_to_tree($list);
pr($tree); // 输出树形结构
```

### `array2xml($arr, $root = '')`
将数组转换为 XML。

**参数：**
- `$arr` (array)：输入数组。
- `$root` (string)：根节点名称（可选）。

**返回值：**
- string：XML 字符串。

**示例：**
```php
$xml = array2xml(['name' => 'John']);
echo $xml; // 输出 XML
```

### `xml2array($xml_content)`
将 XML 转换为数组。

**参数：**
- `$xml_content` (string)：XML 内容。

**返回值：**
- array：转换后的数组。

**示例：**
```php
$array = xml2array('<root><name>John</name></root>');
pr($array); // 输出数组
```

### `yaml($str)`
将 YAML 转换为数组或数组转换为 YAML。

**参数：**
- `$str` (mixed)：输入 YAML 字符串或数组。

**返回值：**
- mixed：转换后的数组或 YAML 字符串。

**示例：**
```php
$array = yaml("name: John");
pr($array); // 输出数组
$yaml = yaml(['name' => 'John']);
echo $yaml; // 输出 YAML
```

### `array_to_pager($arr)`
将数组转换为分页数据。

**参数：**
- `$arr` (array)：输入数组。

**返回值：**
- array：分页数据（包含 `current_page`, `data`, `last_page`, `per_page`, `total`, `total_cur`）。

**示例：**
```php
$list = array_to_pager(['item1', 'item2', 'item3']);
pr($list); // 输出分页数据
```

### `array_to_el_select($all, $v, $k)`
将数组转换为 Element UI 的 select 组件格式。

**参数：**
- `$all` (array)：输入数组。
- `$v` (string)：值字段。
- `$k` (string)：标签字段。

**返回值：**
- array：转换后的数组。

**示例：**
```php
$list = array_to_el_select([['id' => 1, 'name' => 'John'], ['id' => 2, 'name' => 'Jane']], 'id', 'name');
pr($list); // 输出：[['label' => 'John', 'value' => 1], ['label' => 'Jane', 'value' => 2]]
```

---

## 其他功能

### `float_noup($float_number, $dot = 2)`
不进位截取浮点数。

**参数：**
- `$float_number` (float)：输入浮点数。
- `$dot` (int)：保留小数位数（默认 2）。

**返回值：**
- float：截取后的浮点数。

**示例：**
```php
echo float_noup(3.145, 2); // 输出：3.14
```

### `float_up($float_number, $dot = 2, $mid_val = 5)`
四舍五入浮点数。

**参数：**
- `$float_number` (float)：输入浮点数。
- `$dot` (int)：保留小数位数（默认 2）。
- `$mid_val` (int)：进位阈值（默认 5）。

**返回值：**
- float：四舍五入后的浮点数。

**示例：**
```php
echo float_up(3.145, 2); // 输出：3.15
```

### `get_ext_by_url($url)`
获取 URL 的文件扩展名。

**参数：**
- `$url` (string)：文件 URL。

**返回值：**
- string：文件扩展名。

**示例：**
```php
echo get_ext_by_url('https://example.com/file.pdf'); // 输出：pdf
```

### `get_distance($longitude1, $latitude1, $longitude2, $latitude2, $unit = 2, $decimal = 2)`
计算两点间的地理距离。

**参数：**
- `$longitude1` (float)：起点经度。
- `$latitude1` (float)：起点纬度。
- `$longitude2` (float)：终点经度。
- `$latitude2` (float)：终点纬度。
- `$unit` (int)：单位（1: 米，2: 公里，默认 2）。
- `$decimal` (int)：保留小数位数（默认 2）。

**返回值：**
- float：距离。

**示例：**
```php
$dist = get_distance(116.397128, 39.916527, 116.403958, 39.915049);
echo $dist; // 输出距离（公里）
```

### `timeago($time)`
将时间转换为“多久前”的格式。

**参数：**
- `$time` (string|int)：时间戳或日期字符串。

**返回值：**
- string：时间描述。

**示例：**
```php
echo timeago(strtotime('-1 hour')); // 输出：1小时前
```

### `get_barcode($code, $type = 'C128', $widthFactor = 2, $height = 30, $foregroundColor = [0, 0, 0])`
生成条形码图片（Base64 编码）。

**参数：**
- `$code` (string)：条形码内容。
- `$type` (string)：条形码类型（默认 'C128'）。
- `$widthFactor` (int)：宽度因子（默认 2）。
- `$height` (int)：高度（默认 30）。
- `$foregroundColor` (array)：前景色 RGB（默认 [0, 0, 0]）。

**返回值：**
- string：Base64 编码的图片。

**示例：**
```php
$barcode = get_barcode('123456789');
echo "<img src='$barcode' />"; // 输出条形码图片
```

### `validate($labels, $data, $rules, $show_array = false)`
验证数据是否符合规则。

**参数：**
- `$labels` (array)：字段标签。
- `$data` (array)：待验证数据。
- `$rules` (array)：验证规则。
- `$show_array` (bool)：是否返回所有错误（默认 false）。

**返回值：**
- array|null：错误信息或 null。

**示例：**
```php
$vali = validate(
    ['email' => '邮件地址'],
    ['email' => 'test@example.com'],
    ['required' => [['email']], 'email' => [['email']]]
);
if ($vali) {
    json($vali);
}
```

### `create_sign($params, $secret = '', $array_encode = false)`
生成签名。

**参数：**
- `$params` (array)：参与签名的参数。
- `$secret` (string)：签名密钥（默认从配置获取）。
- `$array_encode` (bool)：是否对数组参数进行 JSON 编码（默认 false）。

**返回值：**
- string：大写 MD5 签名。

**示例：**
```php
$sign = create_sign(['id' => 1, 'name' => 'John']);
echo $sign; // 输出签名
```

### `curl_aliyun($url, $bodys = '', $method = 'POST')`
调用阿里云 API。

**参数：**
- `$url` (string)：API 地址。
- `$bodys` (string|array)：请求体（默认空）。
- `$method` (string)：请求方法（默认 'POST'）。

**返回值：**
- array：API 响应。

**示例：**
```php
$response = curl_aliyun('https://api.aliyun.com', ['key' => 'value']);
pr($response); // 输出 API 响应
```

### `array_order_by(...)`
按指定字段对数组进行排序。

**参数：**
- 可变参数：数组、字段名、排序方式（如 SORT_DESC）。

**返回值：**
- array：排序后的数组。

**示例：**
```php
$data = [['id' => 2], ['id' => 1]];
array_order_by($data, 'id', SORT_ASC);
pr($data); // 输出排序后的数组
```

### `call_retry($func, $times = 3, $usleep_time = 1000)`
尝试多次运行回调函数。

**参数：**
- `$func` (callable)：要执行的函数。
- `$times` (int)：尝试次数（默认 3）。
- `$usleep_time` (int)：每次尝试间隔（毫秒，默认 1000）。

**示例：**
```php
call_retry(function() {
    return ['flag' => 'OK'];
}, 3, 1000);
```

### `cookie($name, $value = '', $expire = 0)`
设置或获取 Cookie。

**参数：**
- `$name` (string)：Cookie 名称。
- `$value` (string|array)：Cookie 值（为空时获取，null 时删除）。
- `$expire` (int)：过期时间（秒，默认 0）。

**返回值：**
- mixed：Cookie 值或 null。

**示例：**
```php
cookie('user', 'John', 3600); // 设置 Cookie
echo cookie('user'); // 输出：John
cookie('user', null); // 删除 Cookie
```

### `cookie_delete($name)`
删除指定 Cookie。

**参数：**
- `$name` (string)：Cookie 名称。

**示例：**
```php
cookie_delete('user');
```

### `create_dir_if_not_exists($arr)`
创建目录（如果不存在）。

**参数：**
- `$arr` (string|array)：目录路径或路径数组。

**示例：**
```php
create_dir_if_not_exists('/path/to/dir');
```

### `format_money($money, $len = 2, $sign = '￥')`
格式化金额。

**参数：**
- `$money` (float)：金额。
- `$len` (int)：小数位数（默认 2）。
- `$sign` (string)：货币符号（默认 '￥'）。

**返回值：**
- string：格式化后的金额。

**示例：**
```php
echo format_money(1234.567, 2); // 输出：￥1,234.57
```

### `get_dates($start, $end)`
返回两个日期之间的日期数组。

**参数：**
- `$start` (string)：开始日期（格式 Y-m-d）。
- `$end` (string)：结束日期（格式 Y-m-d）。

**返回值：**
- array：日期数组。

**示例：**
```php
$dates = get_dates('2023-01-01', '2023-01-03');
pr($dates); // 输出：['2023-01-01', '2023-01-02', '2023-01-03']
```

### `get_date_china($date)`
获取日期是星期几（中文）。

**参数：**
- `$date` (string)：日期（格式 Y-m-d）。

**返回值：**
- string：星期几。

**示例：**
```php
echo get_date_china('2023-01-01'); // 输出：日
```

### `get_deep_dir($path)`
获取目录及其子目录列表。

**参数：**
- `$path` (string)：目录路径。

**返回值：**
- array：目录列表。

**示例：**
```php
$dirs = get_deep_dir('/path/to/dir');
pr($dirs); // 输出目录列表
```

### `get_dir($name)`
获取文件路径的目录部分。

**参数：**
- `$name` (string)：文件路径。

**返回值：**
- string：目录路径。

**示例：**
```php
echo get_dir('/path/to/file.txt'); // 输出：/path/to
```

### `get_ext($name)`
获取文件扩展名。

**参数：**
- `$name` (string)：文件路径或 URL。

**返回值：**
- string：扩展名。

**示例：**
```php
echo get_ext('file.txt'); // 输出：txt
```

### `get_name($name)`
获取文件名（不含扩展名）。

**参数：**
- `$name` (string)：文件路径或 URL。

**返回值：**
- string：文件名。

**示例：**
```php
echo get_name('/path/to/file.txt'); // 输出：file
```

### `get_file($id)`
获取文件信息（从数据库）。

**参数：**
- `$id` (string|array)：文件 ID 或查询条件。

**返回值：**
- array：文件信息。

**示例：**
```php
$file = get_file(1);
pr($file); // 输出文件信息
```

### `get_ip($type = 0, $adv = false)`
获取客户端 IP 地址。

**参数：**
- `$type` (int)：返回类型（0: 字符串，1: 整数，默认 0）。
- `$adv` (bool)：是否使用高级方法（默认 false）。

**返回值：**
- string|int：IP 地址。

**示例：**
```php
echo get_ip(); // 输出：127.0.0.1
```

### `get_server_headers($name = '')`
获取服务器请求头。

**参数：**
- `$name` (string)：指定请求头名称（为空时返回所有头）。

**返回值：**
- string|array：请求头值或所有头。

**示例：**
```php
echo get_server_headers('user-agent'); // 输出 User-Agent
```

### `host()`
获取主机地址。

**返回值：**
- string：主机地址。

**示例：**
```php
echo host(); // 输出：https://example.com
```

### `is_ajax()`
判断是否为 AJAX 请求。

**返回值：**
- bool：是否为 AJAX 请求。

**示例：**
```php
if (is_ajax()) {
    echo 'This is an AJAX request';
}
```

### `is_cli()`
判断是否为命令行环境。

**返回值：**
- bool：是否为命令行。

**示例：**
```php
if (is_cli()) {
    echo 'Running in CLI';
}
```

### `is_json($data, $assoc = false)`
判断字符串是否为 JSON 格式。

**参数：**
- `$data` (string)：输入字符串。
- `$assoc` (bool)：是否返回关联数组（默认 false）。

**返回值：**
- mixed：解析后的 JSON 数据或 false。

**示例：**
```php
$json = is_json('{"name":"John"}', true);
pr($json); // 输出数组
```

### `is_local()`
判断是否为本地环境。

**返回值：**
- bool：是否为本地环境。

**示例：**
```php
if (is_local()) {
    echo 'Running locally';
}
```

### `is_post()`
判断是否为 POST 请求。

**返回值：**
- bool：是否为 POST 请求。

**示例：**
```php
if (is_post()) {
    echo 'This is a POST request';
}
```

### `is_ssl()`
判断是否为 HTTPS 协议。

**返回值：**
- bool：是否为 HTTPS。

**示例：**
```php
if (is_ssl()) {
    echo 'Using HTTPS';
}
```

### `now()`
获取当前时间。

**返回值：**
- string：当前时间（格式 Y-m-d H:i:s）。

**示例：**
```php
echo now(); // 输出：2025-07-14 16:58:00
```

### `price_format($yuan, $dot = 2)`
格式化价格。

**参数：**
- `$yuan` (float)：价格。
- `$dot` (int)：小数位数（默认 2）。

**返回值：**
- string：格式化后的价格。

**示例：**
```php
echo price_format(123.456, 2); // 输出：123.46
```

### `show_number($num)`
优化数字显示（去除末尾多余的 0 和小数点）。

**参数：**
- `$num` (float|string)：数字。

**返回值：**
- string：优化后的数字。

**示例：**
```php
echo show_number(1.10); // 输出：1.1
```

### `add_action($name, $call, $level = 20)`
添加动作钩子。

**参数：**
- `$name` (string)：钩子名称。
- `$call` (callable)：回调函数。
- `$level` (int)：优先级（默认 20）。

**示例：**
```php
add_action('my_hook', function($data) {
    echo 'Action triggered';
});
```

### `do_action($name, &$par = null)`
执行动作钩子。

**参数：**
- `$name` (string)：钩子名称。
- `$par` (mixed)：传递给回调函数的参数。

**示例：**
```php
do_action('my_hook');
```

### `install_sql($file, $call)`
从文件中执行 SQL 语句。

**参数：**
- `$file` (string)：SQL 文件路径。
- `$call` (callable)：执行 SQL 的回调函数。

**示例：**
```php
install_sql('script.sql', function($sql) {
    db_query($sql);
});
```

### `install_sql_get_next($fp)`
从文件中逐条获取 SQL 语句。

**参数：**
- `$fp` (resource)：文件句柄。

**返回值：**
- string：SQL 语句。

**示例：**
```php
$fp = fopen('script.sql', 'r');
$sql = install_sql_get_next($fp);
echo $sql; // 输出单条 SQL
```

### `get_ins($key, $call)`
避免重复执行回调函数。

**参数：**
- `$key` (string)：唯一键。
- `$call` (callable)：回调函数。

**示例：**
```php
get_ins('my_key', function() {
    echo 'Run once';
});
```

### `run_cmd_unique($argv, $find = 'php cmd.php')`
防止命令行重复执行。

**参数：**
- `$argv` (array)：命令行参数。
- `$find` (string)：查找的命令（默认 'php cmd.php'）。

**示例：**
```php
run_cmd_unique($argv);
```

### `import($file, $vars = [], $check_vars = false)`
包含 PHP 文件。

**参数：**
- `$file` (string)：文件路径。
- `$vars` (array)：传递的变量。
- `$check_vars` (bool)：是否检查变量（默认 false）。

**返回值：**
- bool：是否包含成功。

**示例：**
```php
import('file.php', ['var' => 'value']);
```

### `set_config($title, $body)`
设置配置项（存入数据库）。

**参数：**
- `$title` (string)：配置名称。
- `$body` (mixed)：配置值。

**示例：**
```php
set_config('site_name', 'My Site');
```

### `get_config($title)`
获取配置项（优先从数据库获取）。

**参数：**
- `$title` (string|array)：配置名称或名称数组。

**返回值：**
- mixed：配置值。

**示例：**
```php
echo get_config('site_name'); // 输出：My Site
```

### `page_size($name)`
获取或设置分页大小。

**参数：**
- `$name` (string)：分页名称。

**返回值：**
- int：分页大小。

**示例：**
```php
echo page_size('users'); // 输出：20
```

### `aes_encode($data, $key = '', $iv = '', $type = 'AES-128-CBC', $options = '')`
AES 加密数据。

**参数：**
- `$data` (mixed)：要加密的数据。
- `$key` (string)：加密密钥。
- `$iv` (string)：初始化向量。
- `$type` (string)：加密类型（默认 'AES-128-CBC'）。
- `$options` (string)：加密选项。

**返回值：**
- string：Base64 编码的加密数据。

**示例：**
```php
$encrypted = aes_encode('secret');
echo $encrypted; // 输出加密字符串
```

### `aes_decode($data, $key = '', $iv = '', $type = 'AES-128-CBC', $options = '')`
AES 解密数据。

**参数：**
- `$data` (string)：Base64 编码的加密数据。
- `$key` (string)：解密密钥。
- `$iv` (string)：初始化向量。
- `$type` (string)：解密类型（默认 'AES-128-CBC'）。
- `$options` (string)：解密选项。

**返回值：**
- mixed：解密后的数据。

**示例：**
```php
$decrypted = aes_decode($encrypted);
echo $decrypted; // 输出：secret
```

### `set_lang($lang = 'zh-cn')`
设置多语言。

**参数：**
- `$lang` (string)：语言代码（默认 'zh-cn'）。

**示例：**
```php
set_lang('en-us');
```

### `lang($name, $val = [], $pre = 'app')`
获取多语言翻译。

**参数：**
- `$name` (string)：翻译键。
- `$val` (array)：替换参数。
- `$pre` (string)：前缀（默认 'app'）。

**返回值：**
- string：翻译后的字符串。

**示例：**
```php
echo lang('hello', ['name' => 'John']); // 输出：Hello, John
```

### `gz_encode($arr_or_str)`
压缩字符串或数组。

**参数：**
- `$arr_or_str` (string|array)：输入数据。

**返回值：**
- string：压缩后的数据。

**示例：**
```php
$compressed = gz_encode(['data' => 'test']);
echo $compressed; // 输出压缩数据
```

### `gz_decode($str)`
解压缩字符串。

**参数：**
- `$str` (string)：压缩数据。

**返回值：**
- mixed：解压缩后的数据。

**示例：**
```php
$decompressed = gz_decode($compressed);
pr($decompressed); // 输出原始数据
```

### `copy_base64_data()`
生成复制 Base64 图片数据的 JavaScript 代码。

**返回值：**
- string：JavaScript 代码。

**示例：**
```php
echo copy_base64_data(); // 输出 JS 代码
```

### `is_image($url)`
判断是否为图片文件。

**参数：**
- `$url` (string)：文件 URL 或路径。

**返回值：**
- bool：是否为图片。

**示例：**
```php
if (is_image('image.jpg')) {
    echo 'This is an image';
}
```

### `is_video($url)`
判断是否为视频文件。

**参数：**
- `$url` (string)：文件 URL 或路径。

**返回值：**
- bool：是否为视频。

**示例：**
```php
if (is_video('video.mp4')) {
    echo 'This is a video';
}
```

### `is_audio($url)`
判断是否为音频文件。

**参数：**
- `$url` (string)：文件 URL 或路径。

**返回值：**
- bool：是否为音频。

**示例：**
```php
if (is_audio('audio.mp3')) {
    echo 'This is an audio';
}
```

### `get_upload_url($f)`
将带 HTTP 的 URL 转换为上传路径格式。

**参数：**
- `$f` (string)：URL 或路径。

**返回值：**
- string：转换后的路径。

**示例：**
```php
echo get_upload_url('https://example.com/uploads/file.jpg'); // 输出：uploads/file.jpg
```

### `pr($str)`
格式化输出数组或字符串。

**参数：**
- `$str` (mixed)：要输出的数据。

**示例：**
```php
pr(['name' => 'John']); // 输出格式化数组
```

### `echats($ele, $options = [])`
生成 ECharts 图表。

**参数：**
- `$ele` (array)：图表元素配置（`id`, `width`, `height`, `class`）。
- `$options` (array)：ECharts 配置。

**返回值：**
- array：包含 JS 代码和 HTML。

**示例：**
```php
$chart = echats(['id' => 'main1', 'width' => 600, 'height' => 400], [
    'title' => ['text' => '示例图表'],
    'xAxis' => ['data' => ['A', 'B', 'C']],
    'series' => [['data' => [5, 20, 36], 'type' => 'bar']]
]);
echo $chart['html'];
```

### `echats_reload()`
刷新 ECharts 图表。

**返回值：**
- string：刷新图表的 JS 代码。

**示例：**
```php
echo echats_reload(); // 输出刷新 JS 代码
```

### `get_browser_lang()`

取当前浏览器语言

~~~
get_browser_lang()
~~~

### `find_files($arr,$return_file = false )`

默认查找文件，找到后包含文件

`$return_file` 为 `true` 时返回找到的文件完整路径。


~~~
find_files($arr = [])
~~~

### `app(class_name)` 

获取应用类实例。

**参数：**
- `$class_name` (string)：类名。

**返回值：**
- object：类实例。

 