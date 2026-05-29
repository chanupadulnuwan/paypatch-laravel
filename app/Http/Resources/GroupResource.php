<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// GroupResource — shapes what a Group looks like in API responses
//
// Instead of dumping the raw Eloquent model (which might expose internals),
// this controls exactly which fields are returned.
// Used by GET /api/groups and GET /api/groups/{id}

class GroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'currency'     => $this->currency,
            // members_count comes from withCount('members') on the query
            'member_count' => $this->members_count ?? $this->members->count(),
            // your_balance is calculated and attached in the controller
            'your_balance' => $this->your_balance ?? 0,
            'created_at'   => $this->created_at->toDateTimeString(),
            // NOTE: password is never included — it's in $hidden on User model
            // and we never expose the creator's full object here
            'created_by'   => [
                'id'   => $this->creator->id,
                'name' => $this->creator->name,
            ],
        ];
    }
}
