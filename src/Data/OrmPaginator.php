<?php


namespace App\Data;


use App\Repository\ResultatRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use JetBrains\PhpStorm\Pure;

/**
 * Class OrmPaginator
 * @package App\Data
 */
class OrmPaginator extends Paginator
{
    /**
     * @var int
     */
    private int $page;

    /**
     * @var int
     */
    private int $pageSize;

    /**
     * @param int $page
     * @param int $limit
     * @return $this
     */
    public function paginate(int $page = 1, int $limit = ResultatRepository::PAGE_SIZE): self
    {
        $page = $page > 1 ? $page : 1;

        $this->page = $page;

        $this->pageSize = $limit;

        $this
            ->getQuery()
            ->setFirstResult($limit * ($page - 1))// Offset
            ->setMaxResults($limit)// Limit
        ;

        return $this;
    }

    /**
     * @return int|null
     */
    #[Pure]
    public function getOffset(): ?int
    {
        return $this->getQuery()->getFirstResult();
    }

    /**
     * @return float|bool|int
     */
    public function getLastPage(): float|bool|int
    {
        $limit = $this->getQuery()->getMaxResults();

        if($limit == 0)
        {
            return 0;
        }

        return ceil($this->count()/$limit);
    }

    /**
     * @return int
     */
    public function page(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    #[Pure]
    public function currentPage(): int
    {
        return $this->page();
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }
}
