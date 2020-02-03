<?php


namespace App\Service;


use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use Knp\Component\Pager\PaginatorInterface;

class CustomPagination
{
    private $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * Custom Pagination with KnpPaginator and HATEOAS
     *
     * @param mixed $dataQuery - anything what needs to be paginated
     * @param string $currentRoute - current route name
     * @param int $page - page number, starting from 1
     * @param int $limit - number of items per page
     * @return PaginatedRepresentation
     */
    public function paginate($dataQuery, $currentRoute, $currentPage, $limit)
    {
        $paginatedData = $this->paginator->paginate($dataQuery, $currentPage, $limit);
        $numberOfPages = (int)ceil($paginatedData->getTotalItemCount() / $limit);
        $collection = new CollectionRepresentation($paginatedData);
        $paginatedResult = new PaginatedRepresentation(
            $collection,
            $currentRoute,
            array(),
            $currentPage,
            $limit,
            $numberOfPages,
            null,
            null,
            false,
            $paginatedData->getTotalItemCount()
        );
        return $paginatedResult;
    }

}