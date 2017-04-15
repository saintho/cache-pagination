<?php
require '../src/CachePagination.php';

$cachePageCount = 1;
$cachePerPageNum = 2;
$offset = 1;
$limit = 1;

$cal = new Saint\CachePagination\CachePagination($cachePageCount, $cachePerPageNum, $offset, $limit);
$cal->exec();
$result = $cal->getResult();

print_r($result);