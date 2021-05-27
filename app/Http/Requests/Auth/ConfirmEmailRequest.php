<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ConfirmEmailRequest extends Request
{
    public function rules()
    {
        return [
            'token' => 'required|string',
            'password' => 'required'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        User::setForceVisibleFields(['password']);
        $user = app(UserService::class)->findBy('set_password_hash', $this->get('token'));
        User::setForceVisibleFields([]);

        if (!$user || !Hash::check($this->get('password'), $user['password']) || empty($user['new_email'])) {
            throw new UnprocessableEntityHttpException(__('validation.exceptions.not_found', ['entity' => 'User']));
        }
    }
}
