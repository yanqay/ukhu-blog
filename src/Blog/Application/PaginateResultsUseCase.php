<?php

declare(strict_types=1);

namespace App\Blog\Application;

class PaginateResultsUseCase
{
    public function handle($results, $page)
    {
        $response = array();

        $perpage = 5;
        $offset = ($page - 1) * $perpage;
        $response['results'] = array_slice($results, $offset, $perpage);

        $totalPages = ceil(count($results) / $perpage);

        $response['pagination'] = array();
        if($page <= $totalPages && $page > 1){
            $response['pagination']['previous_page'] = $page - 1;
        }
        if($page < $totalPages){
            $response['pagination']['next_page'] = $page + 1;
        }

        return $response;
    }
}
