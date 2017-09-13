<?php
namespace Drupal\astrology\service;


class LoggerUtil
{

    public static function log($tag, $msg)
    {
        $logger = new Logger("/tmp/ast");
        $logger->info("[" . $tag . "] ===>>>> ".$msg);
    }
}