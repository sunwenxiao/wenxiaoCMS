<?php
defined('WENXIAOCMS') or exit('Access denied!');
/**
* @author Seraphim
* @copyright 2012
*/
// <!-- 公共的返回header的子程序 -->
function sendheader($last_modified, $p_type, $content_length = 0)
{
// 设置客户端缓存有效时间
header("Expires: " . gmdate("D, d M Y H:i:s", time() + 15360000) . "GMT");
header("Cache-Control: max-age=315360000");
header("Pragma: ");
// 设置最后修改时间
header("Last-Modified: " . $last_modified);
// 设置文件类型信息
header($p_type);
header("Content-Length: " . $content_length);
}
define('ABSPATH', dirname(__file__) . '/');
$cache = true;
$cachedir = 'cache/'; //存放gz文件的目录，确保可写
if (empty($_SERVER['QUERY_STRING']))
exit();
$gzip = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip');
if (empty($gzip))
$cache = false;
$key = array_shift(explode('?', $_SERVER['QUERY_STRING']));
$key = str_replace('../', '', $key);
$filename = ABSPATH . $key;
$symbol = '_';
$rel_path = str_replace(ABSPATH, '', dirname($filename));
$namespace = str_replace('/', $symbol, $rel_path);
$cache_filename = ABSPATH . $cachedir . $namespace . $symbol . basename($filename) .
'.gz'; //生成gz文件路径
$ext = array_pop(explode('.', $filename)); //根据后缀判断文件类型信息
$type = "Content-type: text/html"; //默认的文件类型
switch ($ext)
{
case 'css':
$type = "Content-type: text/css";
break;
case 'js':
$type = "Content-type: text/javascript";
break;
case 'gif':
$cache = false;
$type = "Content-type: image/gif";
break;
case 'jpg':
$cache = false;
$type = "Content-type: image/jpeg";
break;
case 'png':
$cache = false;
$type = "Content-type: image/png";
break;
default:
exit();
}
if ($cache)
{
if (file_exists($cache_filename))
{ // 假如存在gz文件
$mtime = filemtime($cache_filename);
$gmt_mtime = gmdate('D, d M Y H:i:s', $mtime) . ' GMT';
if ((isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && array_shift(explode(';',
 $_SERVER['HTTP_IF_MODIFIED_SINCE'])) ==
$gmt_mtime))
{
// 与浏览器cache中的文件修改日期一致，返回304
header("HTTP/1.1 304 Not Modified");
// 发送客户端header
header("Content-Encoding :gzip");
sendheader($gmt_mtime, $type);
}
else
{
// 读取gz文件输出
$content = file_get_contents($cache_filename);
// 发送客户端header
sendheader($gmt_mtime, $type, strlen($content));
header("Content-Encoding: gzip");
// 发送数据
echo $content;
}
}
else
if (file_exists($filename))
{ // 没有对应的gz文件
$mtime = mktime();
$gmt_mtime = gmdate('D, d M Y H:i:s', $mtime) . ' GMT';
// 读取文件
$content = file_get_contents($filename);
// 去掉空白的部分
// $content = ltrim($content);
// 压缩文件内容
$content = gzencode($content, 9, $gzip ? FORCE_GZIP : FORCE_DEFLATE);
// 发送客户端header
sendheader($gmt_mtime, $type, strlen($content));
header("Content-Encoding: gzip");
// 发送数据
echo $content;
// 写入文件
file_put_contents($cache_filename, $content);
}
else
{
header("HTTP/1.0 404 Not Found");
}
}
else
{ // 处理不使用Gzip模式下的输出。原理基本同上
if (file_exists($filename))
{
$mtime = filemtime($filename);
$gmt_mtime = gmdate('D, d M Y H:i:s', $mtime) . ' GMT';
if ((isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && array_shift(explode(';',
$_SERVER['HTTP_IF_MODIFIED_SINCE'])) ==
$gmt_mtime))
{
// 与浏览器cache中的文件修改日期一致，返回304
header("HTTP/1.1 304 Not Modified");
// 发送客户端header
sendheader($gmt_mtime, $type, strlen($content));
header("Content-Encoding :gzip");
}
else
{
// 读取文件输出
$content = file_get_contents($filename);
// 发送客户端header
sendheader($gmt_mtime, $type, strlen($content));
// 发送数据
echo $content;
}
}
else
{
header("HTTP/1.0 404 Not Found");
}
}
?>