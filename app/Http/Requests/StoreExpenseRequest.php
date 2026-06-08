<?php

namespace App\Http\Requests;

use App\Models\Group;
use Illuminate\Foundation\Http\FormRequest;

// StoreExpenseRequest — Form Request for POST /api/expenses
//
// Two jobs:
//   1. authorize() — checks the logged-in user is a member of the target group
//   2. rules()     — validates the incoming JSON fields
//
// Laravel automatically runs this before the controller method runs.
// If authorize() returns false → 403 response.
// If rules() fail → 422 Unprocessable Entity with error details.

class StoreExpenseRequest extends FormRequest
{
    // Check that the authenticated user is actually in the group they're posting to
    public function authorize(): bool
    {
        $group = Group::find($this->input('group_id'));

        if (!$group) {
            return false;
        }

        // belongsToMany check — is this user a member of that group?
        return $group->members()->where('users.id', $this->user()->id)->exists();
    }

    // Validation rules applied to the request body
    public function rules(): array
    {
        return [
            'group_id'      => ['required', 'integer', 'exists:groups,id'],
            'title'         => ['required', 'string', 'max:255'],
            'amount'        => ['required', 'numeric', 'min:0.01'],
            'paid_by'       => ['required', 'integer', 'exists:users,id'],
            'receipt_image'     => ['nullable', 'image', 'max:4096'],
            'location'          => ['nullable', 'string', 'max:100'],
            'split_member_ids'  => ['nullable', 'array'],
            'split_member_ids.*'=> ['integer', 'exists:users,id'],
        ];
    }

    // Custom error messages (optional but nice to have)
    public function messages(): array
    {
        return [
            'group_id.exists' => 'That group does not exist.',
            'paid_by.exists'  => 'That user does not exist.',
            'amount.min'      => 'Amount must be at least LKR 0.01.',
        ];
    }
}
