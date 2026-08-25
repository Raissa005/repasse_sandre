<?php

function normalize ($filename) {

    $file_contents = file_get_contents($filename);

    if (!$file_contents) {
        echo "Could not convert the ending-lines : impossible to load the file.PHP_EOL";
        return false;
    }

    $DontReplaceThisString = "\r\n";
    $specialString = "!£#!Dont_wanna_replace_that!#£!";
    $string = str_replace($DontReplaceThisString, $specialString, $file_contents);

    $file_contents = str_replace("\r", "\r\n", $file_contents);

    $file_contents = str_replace($DontReplaceThisString, $specialString, $file_contents);

    $file_contents = str_replace("\n", "\r\n", $file_contents);

    $file_contents = str_replace($specialString, $DontReplaceThisString, $file_contents);

    file_put_contents($filename, $file_contents);

    return true;
}