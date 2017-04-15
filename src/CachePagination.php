<?php


namespace Saint\CachePagination;


class CachePagination
{
    protected $cachePageCount;
    protected $cachePerPageNum;
    protected $offset;
    protected $limit;
    protected $overBudget = true;
    protected $result;

    public function __construct($cachePageCount, $cachePerPageNum, $offset, $limit)
    {
        $this->cachePageCount = $cachePageCount;
        $this->cachePerPageNum = $cachePerPageNum;
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public function OverBudget()
    {
        return $this->overBudget;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function exec()
    {
        $cachePageCount = $this->cachePageCount;
        $cachePerPageNum = $this->cachePerPageNum;
        $offset = $this->offset;
        $limit = $this->limit;
        /**
         * check over budget
         */
        if ($this->checkOverBudget($cachePageCount, $cachePerPageNum, $offset, $limit)) {
            return $this->result = false;
        }
        $this->overBudget = false;

        /**
         * 计算分页位置
         */
        $this->result = $this->getCachePageAndPosition($cachePageCount, $cachePerPageNum, $offset, $limit);
    }


    /**
     * 根据传入的offset获取cache的的分页位置
     * @param $cachePageCount
     * @param $cachePageSize
     * @param $selectLimit
     * @param $selectOffset
     * @return array
     */
    protected function getCachePageAndPosition($cachePageCount, $cachePageSize, $selectLimit, $selectOffset)
    {
        /**
         * 设定分页
         */
        $startPage = floor($selectOffset / $cachePageSize);
        $endPage = floor(($selectOffset + $selectLimit - 1) / $cachePageSize);
        $diff = $endPage - $startPage;
        //同一页的情况
        if ($diff == 0) {
            $page = [$startPage];
        } //不同页的情况
        else {
            for ($i = 0; $i <= $diff; $i++) {
                $page[] = $startPage + $i;
            }
        }
        /**
         * 设定分页起始
         */
        $offset = [];
        for ($i = 0; $i <= $diff; $i++) {
            //只有一页的情况
            if ($diff == 0) {
                $startOffset = $selectOffset - $page[$i] * $cachePageSize;
                $offset[] = [$startOffset, $startOffset + $selectLimit];
            }
            //有两页的情况
            if ($diff == 1) {
                if ($i == 0) {
                    $startOffset = $selectOffset - $page[$i] * $cachePageSize;
                    $offset[] = [$startOffset];
                }
                if ($i == 1) {
                    $endLimit = $selectLimit - ($cachePageSize - $startOffset);
                    $offset[] = [0, $endLimit];
                }
            }
            //有多页的情况
            if ($diff > 1) {
                if ($i == 0) {
                    $startOffset = $selectOffset - $page[$i] * $cachePageSize;
                    $offset[] = [$startOffset];
                } elseif ($i == $diff) {
                    $endLimit = $selectLimit - ($cachePageSize - $startOffset) - ($diff - 1) * $cachePageSize;
                    $offset[] = [0, $endLimit];
                } else {
                    $offset[] = [0];
                }
            }
        }

        /**
         * 构造查询信息
         */
        $cacheOffset = [
            'page' => $page,
            'offset' => $offset
        ];

        return $cacheOffset;
    }

    private function checkOverBudget($cachePageCount, $cachePerPageNum, $offset, $length)
    {
        $totalCount = $cachePageCount * $cachePerPageNum;
        if ($totalCount < ($offset + $length)) {
            return true;
        }
        return false;
    }


}