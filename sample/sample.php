<?php
require '../src/CachePagination.php';

/**
 * 一页的情况
 */
$cachePageCount = 10;
$cachePerPageNum = 9;
$offset = 8;
$limit = 21;

$cal = new Saint\CachePagination\CachePagination($cachePageCount, $cachePerPageNum, $limit, $offset);
$cal->exec();
$result = $cal->getResult();

print_r($result);