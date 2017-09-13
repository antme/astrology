<?php
namespace Drupal\astrology\service;


class LoggerUtil
{

    public static function log($tag, $msg)
    {
        $logger = new Logger("/tmp/ast");
        $logger->info("[" . $tag . "] ===>>>> ".$msg);
    }
    
    public static function log1($msg)
    {
        $logger = new Logger("/tmp/ast");
        $logger->info($msg);
    }
    
}