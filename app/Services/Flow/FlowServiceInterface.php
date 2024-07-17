<?php
namespace App\Services\Flow;

use Illuminate\Contracts\Pagination\CursorPaginator;
use stdClass;

interface FlowServiceInterface
{

    public function validate(array $data);
    public function create();

}
