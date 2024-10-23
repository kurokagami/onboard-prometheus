<?php
namespace Framework;
use Exception;

final class TemplateDirectoryHelper{

    public static function buildTemplatePath($dir,$content){
        $slash = DIRECTORY_SEPARATOR;
        if(preg_match("/^@\b/", $content)){
            $content = substr($content,1);
        }elseif(($ex =preg_match("/^app\:\b/", $content)) || strpos($content, ':') === false){
            $dir = basename($dir) != 'app' ? dirname($dir,2) : $dir;
            $content = ($ex ? substr($content, 4) : $content);
        }elseif(strpos($content, ':') !== false){
            $parts=explode(':', $content, 2);
            $module = $parts[0];
            $content = $parts[1];
            $rdir = basename($dir) != 'app' ? dirname($dir,2) : $dir;
            $dir= "{$rdir}{$slash}app_modules{$slash}{$module}";
        }
        return "{$dir}{$slash}views{$slash}{$content}";
    }
}
