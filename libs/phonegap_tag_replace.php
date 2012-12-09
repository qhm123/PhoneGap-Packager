<?php

function get_extension($file_name)
{
  $extend = pathinfo($file_name);
  $extend = strtolower($extend["extension"]);
  return $extend;
}

function replace_phonegap_tag($path)
{
  $filelist = my_scandir($path);
  foreach ($filelist as $file) {
    if (get_extension($file) == "html") {
      $path_prefix = "";
      echo $file . "\n";
      $wwwpos = strpos($file, "www/");
      $filename = substr($file, $wwwpos);
      for($i = 0; $i < substr_count($filename, "/") - 1; $i++)
      {
        $path_prefix .= "../";
      }

      $content = file_get_contents($file);
      $new_content = preg_replace( "/<phonegap>.*<\/phonegap>/is", "<script type='text/javascript' charset='utf-8' src='" . $path_prefix . "phonegap.js'></script>", $content);
      file_put_contents($file, $new_content);
    }
  }
}

function my_scandir($path)
{
  $filelist=array();
  if($handle=opendir($path)){
    while (($file=readdir($handle))!==false){
      if($file!="." && $file !=".."){
        if(is_dir($path."/".$file)){
          $filelist=array_merge($filelist,my_scandir($path."/".$file));
        }else{
          $filelist[]=$path."/".$file;
        }
      }
    }
  }
  closedir($handle);
  return $filelist;
}
