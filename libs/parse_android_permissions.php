<?php

function parse_android_permissions($file)
{
  $content = file_get_contents($file);
  $splits = preg_split('/=====/', $content);
  for($i = 0; $i < sizeof($splits); $i++)
  {
    $temp = explode("\n", trim($splits[$i]));
    $id = $temp[0];
    array_shift($temp);
    $results[strval($id)] = $temp;
  }

  return $results;
}

?>
