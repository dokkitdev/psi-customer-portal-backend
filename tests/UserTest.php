<?php

namespace App\Tests;

use App\Mails\InvitationMail;
use App\Models\User;
use App\Tests\Support\AuthTestTrait;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class UserTest extends TestCase
{
    use AuthTestTrait;

    protected $admin;
    protected $user;

    protected array $requiredOriginStates = [
        'users',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::find(1);
        $this->user = User::find(2);
    }

    public function testCreate()
    {
        $this->mockUniqueTokenGeneration('some_token');

        $data = $this->getJsonFixture('create_user.json');

        $response = $this->actingAs($this->admin)->json('post', '/users', $data);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('user_created.json', $response->json());

        $this->assertDatabaseHas('users', $this->getJsonFixture('user_created_database.json'));

        $this->assertDatabaseHas('group_user', ['user_id' => 3, 'group_id' => 1]);
        $this->assertDatabaseHas('group_user', ['user_id' => 3, 'group_id' => 2]);

        $this->assertMailEquals(InvitationMail::class, [
            [
                'emails' => $data['email'],
                'fixture' => 'invitation_email.html'
            ]
        ]);
    }

    public function testCreateNoAuth()
    {
        $data = $this->getJsonFixture('create_user.json');

        $response = $this->json('post', '/users', $data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseMissing('users', Arr::except($data, ['password', 'is_send_email', 'group_ids']));
    }

    public function testCreateNoPermission()
    {
        $data = $this->getJsonFixture('create_user.json');

        $response = $this->actingAs($this->user)->json('post', '/users', $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('users', Arr::except($data, ['password', 'is_send_email', 'group_ids']));
    }

    public function testCreateUserExists()
    {
        $response = $this->actingAs($this->admin)->json('post', '/users', $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdate()
    {
        $data = $this->getJsonFixture('update_user.json');

        $response = $this->actingAs($this->admin)->json('put', '/users/2', $data);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('users', Arr::except($data, 'group_ids'));

        $this->assertDatabaseMissing('group_user', ['user_id' => 2, 'group_id' => 1]);
        $this->assertDatabaseHas('group_user', ['user_id' => 2, 'group_id' => 2]);
        $this->assertDatabaseHas('group_user', ['user_id' => 2, 'group_id' => 3]);
    }

    public function testUpdateByUser()
    {
        $data = $this->getJsonFixture('update_user.json');

        $response = $this->actingAs($this->user)->json('put', '/users/2', $data);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('users', Arr::except($data, 'group_ids'));

        $this->assertDatabaseHas('users', Arr::except($data, ['invoice_permission_level', 'quote_permission_level', 'is_quote_requests', 'is_job_requests', 'group_ids']));

        $this->assertDatabaseHas('group_user', ['user_id' => 2, 'group_id' => 1]);
        $this->assertDatabaseHas('group_user', ['user_id' => 2, 'group_id' => 2]);
    }

    public function testUpdateNoPermission()
    {
        $data = $this->getJsonFixture('update_user.json');

        $response = $this->actingAs($this->user)->json('put', '/users/1', $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => $data['name'],
            'email' => $data['email']
        ]);
    }

    public function testUpdateWithEmailOfAnotherUser()
    {
        $response = $this->actingAs($this->admin)->json('put', '/users/2', [
            'email' => 'admin@example.com'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseMissing('users', [
            'id' => 2,
            'email' => 'admin@example.com'
        ]);
    }

    public function testUpdateNotExists()
    {
        $data = $this->getJsonFixture('update_user.json');

        $response = $this->actingAs($this->admin)->json('put', '/users/0', $data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdateNoAuth()
    {
        $data = $this->getJsonFixture('update_user.json');

        $response = $this->json('put', '/users/1', $data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseMissing('users', Arr::except($data, 'group_ids'));
    }

    public function testUpdateProfile()
    {
        $this->mockUniqueTokenGeneration('some_token');

        $data = $this->getJsonFixture('update_user.json');

        $response = $this->actingAs($this->admin)->json('put', '/profile', $data);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertEqualsFixture('update_profile_fixture.json', User::query()->find($this->admin->id)->toArray());
    }

    public function testUpdateProfileWithPassword()
    {
        $data = $this->getJsonFixture('update_profile_with_password.json');

        $response = $this->actingAs($this->user)->json('put', '/profile', $data);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testUpdateProfileWithPasswordInvalidPassword()
    {
        $data = $this->getJsonFixture('update_profile_with_password_invalid_password.json');

        $response = $this->actingAs($this->user)->json('put', '/profile', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateProfileWithPasswordEmptyOldPassword()
    {
        $data = $this->getJsonFixture('update_profile_with_password_without_old.json');

        $response = $this->actingAs($this->user)->json('put', '/profile', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateProfileWithPasswordWrongOldPassword()
    {
        $data = $this->getJsonFixture('update_profile_with_password_with_wrong_old.json');

        $response = $this->actingAs($this->user)->json('put', '/profile', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateProfileNoAuth()
    {
        $data = $this->getJsonFixture('update_user.json');

        $response = $this->json('put', '/profile', $data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseMissing('users', Arr::except($data, 'group_ids'));
    }

    public function testDelete()
    {
        $response = $this->actingAs($this->admin)->json('delete', '/users/1');

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('users', [
            'id' => 1
        ]);
    }

    public function testDeleteNotExists()
    {
        $response = $this->actingAs($this->admin)->json('delete', '/users/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDeleteNoAuth()
    {
        $response = $this->json('delete', '/users/1');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseHas('users', [
            'id' => 1
        ]);
    }

    public function testGetProfile()
    {
        $response = $this->actingAs($this->admin)->json('get', '/profile');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_user.json', $response->json());
    }

    public function testGet()
    {
        $response = $this->actingAs($this->admin)->json('get', '/users/1');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_user.json', $response->json());
    }

    public function testGetNotExists()
    {
        $response = $this->actingAs($this->admin)->json('get', '/users/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetDashboardByAdmin()
    {
        $response = $this->actingAs($this->admin)->json('get', '/dashboard');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_dashboard_by_admin.json', $response->json());
    }

    public function testGetDashboardByUser()
    {
        $response = $this->actingAs($this->user)->json('get', '/dashboard');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_dashboard_by_user.json', $response->json());
    }

    public function testGetDashboardNoAuth()
    {
        $response = $this->json('get', '/dashboard');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function getSearchFilters()
    {
        return [
            [
                'filter' => ['all' => 1],
                'result' => 'search_by_all_user.json'
            ],
            [
                'filter' => [
                    'page' => 1,
                    'per_page' => 2,
                ],
                'result' => 'search_by_page_per_page_user.json'
            ],
            [
                'filter' => ['query' => 'Another User'],
                'result' => 'get_users_by_name.json'
            ],
            [
                'filter' => ['query' => 'admin@example.com'],
                'result' => 'get_users_by_email.json'
            ],
            [
                'filter' => ['query' => 'Admin'],
                'result' => 'get_users_by_query.json'
            ],
            [
                'filter' => [
                    'name_query' => 'Mr Admin',
                    'email_query' => 'admin@example.com',
                    'simpro_customer_id' => 1,
                    'query' => 'Admin',
                    'order_by' => 'created_at',
                    'desc' => false
                ],
                'result' => 'get_users_complex.json'
            ],
            [
                'filter' => [
                    'desc' => false,
                    'order_by' => 'name'
                ],
                'result' => 'get_users_check_order.json'
            ]
        ];
    }

    /**
     * @dataProvider  getSearchFilters
     *
     * @param array $filter
     * @param string $fixture
     */
    public function testSearch($filter, $fixture)
    {
        $response = $this->actingAs($this->admin)->json('get', '/users', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture($fixture, $response->json());
    }

    public function testResendInvitation()
    {
        $this->mockUniqueTokenGeneration('some_token');

        $response = $this->actingAs($this->admin)->json('post', '/users/1/resend-invitation');

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'set_password_hash' => 'some_token',
            'set_password_hash_created_at' => Carbon::now()
        ]);

        $this->assertMailEquals(InvitationMail::class, [
            [
                'emails' => 'admin@example.com',
                'fixture' => 'invitation_email.html'
            ]
        ]);
    }

    public function testResendInvitationNotExists()
    {
        $response = $this->actingAs($this->admin)->json('post', '/users/0/resend-invitation');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testResendInvitationNoPermissions()
    {
        $response = $this->actingAs($this->user)->json('post', '/users/1/resend-invitation');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testResendInvitationNoAuth()
    {
        $response = $this->json('post', '/users/1/resend-invitation');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function getDataForTestGenerateResetPasswordLink(): array
    {
        return [
            [
                'acting_as_user_id' => 1,
                'target_user_id' => 2,
                'status_code' => Response::HTTP_OK,
                'response_fixture' => 'generate_reset_password_link__success__response',
                'users_state_fixture' => 'generate_reset_password_link__success__users_state',
            ],
            [
                'acting_as_user_id' => 2,
                'target_user_id' => 2,
                'status_code' => Response::HTTP_FORBIDDEN,
                'response_fixture' => 'generate_reset_password_link__no_permissions__response',
                'users_state_fixture' => null,
            ],
            [
                'acting_as_user_id' => 1,
                'target_user_id' => 9999,
                'status_code' => Response::HTTP_NOT_FOUND,
                'response_fixture' => 'generate_reset_password_link__not_found__response',
                'users_state_fixture' => null,
            ],
            [
                'acting_as_user_id' => null,
                'target_user_id' => 2,
                'status_code' => Response::HTTP_UNAUTHORIZED,
                'response_fixture' => 'generate_reset_password_link__no_auth__response',
                'users_state_fixture' => null,
            ],
        ];
    }

    /**
     * @dataProvider getDataForTestGenerateResetPasswordLink
     */
    public function testGenerateResetPasswordLink(
        ?int $actingAsUserId,
        int $targetUserId,
        int $statusCode,
        string $responseFixture,
        ?string $usersStateFixture
    ): void {
        if ($statusCode === Response::HTTP_OK) {
            $this->mockUniqueTokenGeneration('qwe-random-token-rty');
        }

        if (isset($actingAsUserId)) {
            $this->actingAs(User::find($actingAsUserId));
        }

        $response = $this->json('get', "/users/{$targetUserId}/reset-password-link");

        $response->assertStatus($statusCode);

        $this->assertEqualsFixture($responseFixture, $response->json());

        if (isset($usersStateFixture)) {
            $this->assertChangesEqualsFixture('users', $usersStateFixture);
        } else {
            $this->assertNoChanges('users');
        }
    }
}
