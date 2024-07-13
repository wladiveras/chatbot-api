<?php
namespace App\Services\Flow;

use Illuminate\Contracts\Pagination\CursorPaginator;
use stdClass;

interface FlowServiceInterface
{

    public function findAllUsers();

}
