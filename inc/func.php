<?php
if(!function_exists('pr')){
    function pr($arr, $die = 0){
        echo "<pre>";
            print_r($arr);
        echo ($die)?die():"</pre>";
    }
}
if(!function_exists('e')){
    function e($text){
        if(is_array($text) || is_object($text)) print_r($text);
        else  echo $text;
    }
}
if(!function_exists('write_file')){
    function write_file($filename, $data, $append_mode = false){
        if(file_exists($filename) && $append_mode){
            $fh = fopen($filename, 'a') or die("can't open file");
            fwrite($fh, $data);
            fclose($fh);
        }else{
            $fh = fopen($filename, 'w') or die("can't open file");
            fwrite($fh, $data);
            fclose($fh);
        }
    }
}


function slugify($text)
{
  // replace non letter or digits by -
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  // trim
  $text = trim($text, '-');

  // remove duplicate -
  $text = preg_replace('~-+~', '-', $text);

  // lowercase
  $text = strtolower($text);

  if (empty($text)) {
    return 'n-a';
  }

  return $text;
}
