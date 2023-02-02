<?php /** @noinspection PhpMissingReturnTypeInspection */

/** @noinspection DuplicatedCode */

namespace common\libs\manticore;

use Manticoresearch\ResultSet;

/**
 * Class Result
 * @package common\libs\manticore
 */
class Result
{
    private ?ResultSet $result = null;
    private int $total = 0;
    private array $rows = [];
    private array $ids = [];

    public function setRawResult(ResultSet $result): self
    {
        $this->result = $result;
        return $this;
    }

    public function getRawResult(): ResultSet
    {
        return $this->result;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;
        return $this;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function setRows(array $rows): self
    {
        $this->rows = $rows;
        return $this;
    }

    public function getIds(): array
    {
        return $this->ids;
    }

    public function setIds(array $ids): self
    {
        $this->ids = $ids;
        return $this;
    }
}
