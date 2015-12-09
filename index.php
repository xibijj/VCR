<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="title" content="验证码自动识别程序 - By Mr.x">
<meta name="description" content="验证码自动识别,Mail:coolxia@foxmail.com">
<meta name="keywords" content="验证码自动识别">
<title>验证码自动识别测试</title>

<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="POST">
	<fieldset>
		<label>ImgURL</label><input type="text" style="width:500px" name="url" placeholder="请输入验证码地址"/ value="<?php if ( !empty($_POST['url']) ) echo $_POST['url']; ?>" > 
		<button type="submit" class="btn">提交</button>
	</fieldset>
</form>

<?php
error_reporting(7);

if (!extension_loaded('curl')) exit('请开启CURL扩展,谢谢!');

if ( !empty($_POST['url']) ){
	echo "<br> 验证码: <img src='vcode.png?_".time()."'> 识别: <font size='3' color='red'>".mkvcode($_POST['url'])."</font> Time:".date("Y-m-d H:i",time());
}

function mkvcode($url)
{
    $vcode = '';
	$f_name = 'vcode.png';
    //$vcode_url = "http://jzl.qhlly.cn/image.jsp";
	$vcode_url = $url;
	
    //$pic = send_pack('GET', $vcode_url);
    //file_put_contents($f_name, $pic);
	
	download_remote_file_with_fopen($vcode_url, $f_name);
	
	usleep(50000);
    $cmd = "tesseract $f_name vcode";
	system($cmd);
    if (file_exists('vcode.txt')) {
        $vcode = file_get_contents('vcode.txt');
        $vcode = trim($vcode);
        $vcode = str_replace(' ', '', $vcode);
    }
    if (strlen($vcode) > 3) {
        return $vcode;
    } else {
        return mkvcode2($f_name);//默认模式识别不出来就对图片进行灰度化、裁剪处理再次识别
    }
}

function mkvcode2($f_name)
{
    $vcode = '';
	$tmp_vcode = 'vcode_tmp.png';
	
	include 'img.class.php';
	$image=new image($f_name, 2, "0,0", "80,20", $tmp_vcode);
	$image->outimage();
	
	usleep(50000);
    $cmd = "tesseract $tmp_vcode vcode";
    system($cmd);
    if (file_exists('vcode.txt')) {
        $vcode = file_get_contents('vcode.txt');
        $vcode = trim($vcode);
        $vcode = str_replace(' ', '', $vcode);
    }
    if (strlen($vcode) > 3) {
        return $vcode;
    } else {
        //return mkvcode();
    }
}

//数据包发送函数
function send_pack($method, $url, $post_data = array())
{
    //$cookie = 'saeut=218.108.135.246.1416190347811282;PHPSESSID=6eac12ef61de5649b9bfd8712b0f09c2';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    if ($method == 'POST') {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    }
    $data = curl_exec($curl);
    curl_close($curl);
    return $data;
}

function download_remote_file_with_fopen($file_url, $save_to)
{
	$in=    fopen($file_url, "rb");
	$out=   fopen($save_to, "wb");

	while ($chunk = fread($in,8192))
	{
		fwrite($out, $chunk, 8192);
	}

	fclose($in);
	fclose($out);
}
