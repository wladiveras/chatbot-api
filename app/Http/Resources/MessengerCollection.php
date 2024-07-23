<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MessengerCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'messages' => $this->collection,
        ];
    }
}
