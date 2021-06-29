<?php

namespace App\Services;

use App\Jobs\SendMailJob;
use App\Mails\ForgotPasswordMail;
use App\Mails\InvitationMail;
use App\Models\Role;
use Carbon\Carbon;
use App\Repositories\UserRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * @property UserRepository $repository
 * @mixin UserRepository
 */
class UserService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setRepository(UserRepository::class);
    }

    public function search($filters)
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterBy('role_id')
            ->filterBy('groups.simpro_customer_id')
            ->filterByQuery(['name', 'email'])
            ->filterByQueryWithValue('name', 'name_query')
            ->filterByQueryWithValue('email', 'email_query')
            ->with()
            ->getSearchResults();
    }

    public function create($data)
    {
        $data['role_id'] = Role::USER;
        $data['password'] = Hash::make($this->generateHash());
        $data['set_password_hash'] = $this->generateHash();
        $data['set_password_hash_created_at'] = Carbon::now();

        $user = DB::transaction(function () use ($data) {
            $user = $this->repository
                ->force()
                ->create($data);

            if (Arr::has($data, 'group_ids')) {
                $user->groups()->sync($data['group_ids']);
            }

            return $user;
        });

        if (Arr::get($data, 'is_send_email')) {
            $this->sendInvitationEmail($data['email'], $data['set_password_hash']);
        }

        return $user;
    }

    public function resendInvitation($id)
    {
        $data = [
            'set_password_hash' => $this->generateHash(),
            'set_password_hash_created_at' => Carbon::now()
        ];

        $user = $this->repository
            ->force()
            ->update($id, $data);

        $this->sendInvitationEmail($user['email'], $data['set_password_hash']);
    }

    public function update($where, $data)
    {
        $authUser = $this->getAuthUser();

        if (!$authUser || ($authUser['role_id'] !== Role::ADMIN)) {
            $data = Arr::only($data, ['password', 'email', 'name', 'last_login']);
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = DB::transaction(function () use ($where, $data) {
            $user = $this->repository
                ->force()
                ->update($where, $data);

            if (Arr::has($data, 'group_ids')) {
                $user->groups()->sync($data['group_ids']);
            }

            return $user;
        });

        return $user;
    }

    public function forgotPassword($email)
    {
        $hash = $this->generateHash();

        $this->repository
            ->force()
            ->update([
                'email' => $email
            ], [
                'set_password_hash' => $hash,
                'set_password_hash_created_at' => Carbon::now()
            ]);

        $mail = new ForgotPasswordMail($email, ['hash' => $hash]);
        dispatch(new SendMailJob($mail));
    }

    public function restorePassword($token, $password)
    {
        $this->repository
            ->force()
            ->update([
                'set_password_hash' => $token
            ], [
                'password' => Hash::make($password),
                'set_password_hash' => null
            ]);
    }

    public function confirmEmail($token)
    {
        $user = $this->repository->findBy('set_password_hash', $token);

        $this->repository
            ->force()
            ->update($user['id'], [
                'email' => $user['new_email'],
                'new_email' => null,
                'set_password_hash' => null
            ]);
    }

    protected function sendInvitationEmail($email, $hash)
    {
        $mail = new InvitationMail($email, ['hash' => $hash]);
        dispatch(new SendMailJob($mail));
    }

    protected function generateHash($length = 32)
    {
        $length /= 2;

        return bin2hex(openssl_random_pseudo_bytes($length));
    }
}
