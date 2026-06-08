<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentUserId = $request->user()?->id;
        $coverPath = $this->cover_image_path;
        $coverPreset = null;
        $coverUrl = null;

        if ($coverPath) {
            if (str_starts_with($coverPath, 'preset:')) {
                $coverPreset = str_replace('preset:', '', $coverPath);
            } else {
                $coverUrl = url($coverPath);
            }
        }

        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'currency'          => $this->currency,
            'member_count'      => $this->members_count ?? $this->members->count(),
            'your_balance'      => (double) ($this->your_balance ?? 0),
            'total_expenses'    => round((double) ($this->total_expenses ?? $this->expenses->sum('amount')), 2),
            'created_at'        => optional($this->created_at)->toDateTimeString(),
            'created_by'        => [
                'id'   => $this->creator->id ?? $this->created_by,
                'name' => $this->creator->name ?? 'Owner',
            ],
            'can_edit'          => $currentUserId !== null && (int) $this->created_by === (int) $currentUserId,
            'cover_image_url'   => $coverUrl,
            'cover_image_preset'=> $coverPreset,
            'profile_image_url' => $this->profile_image_path ? url($this->profile_image_path) : null,
            'settlements'       => $this->whenLoaded('settlements', function () {
                return $this->settlements->map(fn ($s) => [
                    'from_user_id' => (int) $s->from_user_id,
                    'to_user_id'   => (int) $s->to_user_id,
                    'amount'       => (float) $s->amount,
                ])->values();
            }),
            'expenses'          => $this->whenLoaded('expenses', function () use ($currentUserId) {
                return $this->expenses->map(function ($expense) use ($currentUserId) {
                    return [
                        'id'                => $expense->id,
                        'title'             => $expense->title,
                        'amount'            => (double) $expense->amount,
                        'paid_by'           => $expense->paid_by,
                        'paid_by_name'      => $expense->paidBy->name ?? 'User',
                        'created_by_id'     => $expense->created_by,
                        'created_by_name'   => $expense->createdBy->name ?? 'User',
                        'can_delete'        => $currentUserId !== null && (int) $expense->created_by === (int) $currentUserId,
                        'receipt_image_url' => $expense->receipt_image_path ? url($expense->receipt_image_path) : null,
                        'created_at'        => optional($expense->created_at)->toDateTimeString(),
                    ];
                })->values();
            }),
        ];
    }
}
