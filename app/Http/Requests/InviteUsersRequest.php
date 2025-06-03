<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\Group;
use App\Models\GroupUser;
use Illuminate\Support\Facades\Auth;
use App\Http\Enums\GroupUserStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class InviteUsersRequest extends FormRequest
{

    public Group $group;
    public ?User $user = null;
    public ?GroupUser $groupUser = null;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /**
         * @var \App\Models\Group $group
         */
        $this->group = $this->route('group');

        return $this->group->isAdmin(Auth::id());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                function ($attribute, $value, \Closure $fail) {
                    // Attempt to find a user by email or username
                    $this->user = User::where('email', $value)
                        ->orWhere('username', $value)
                        ->first();

                    // Fail if no matching user is found
                    if (!$this->user) {
                        $fail('User does not exist');
                        return;
                    }

                    // Check if user is already a member of the group
                    $this->groupUser = GroupUser::where('user_id', $this->user->id)
                        ->where('group_id', $this->group->id)
                        ->first();

                    // Fail if the user is already an approved member
                    if (
                        $this->groupUser &&
                        $this->groupUser->status === GroupUserStatusEnum::APPROVED->value
                    ) {
                        $fail('User is already a member');
                    }
                }
            ],
        ];
    }
}
