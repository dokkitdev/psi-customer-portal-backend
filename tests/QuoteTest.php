<?php

namespace App\Tests;

use App\Models\Job;
use App\Models\Quote;
use App\Models\SimproCustomer;
use App\Models\SimproJob;
use App\Models\SimproSite;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class QuoteTest extends TestCase
{
    use SimproTestTrait;

    protected $admin;
    protected $user;
    protected $userWithoutPermissions;
    protected $userOnlyView;
    protected $files;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::find(1);
        $this->user = User::find(2);
        $this->userWithoutPermissions = User::find(3);
        $this->userOnlyView = User::find(4);
        $this->files = [
            UploadedFile::fake()->image('file1.png', 600, 600),
            UploadedFile::fake()->image('file2.png', 600, 600)
        ];
    }

    public function testUpdateQuoteEvent()
    {
        $this->mockCreateOrUpdateQuote();

        $this->createSimproJob('simpro_webhook_quote_updated_fixture.json');

        $this->artisan('simpro:handle-jobs')->assertExitCode(0);

        $simproJob = SimproJob::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_jobs_fixture.json', $simproJob);

        $quotes = Quote::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('quote_create_or_update_event_fixture.json', $quotes);

        $simproCustomer = SimproCustomer::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_customer_create_or_update_event_fixture.json', $simproCustomer);

        $simproSite = SimproSite::orderBy('id')->with(['site_custom_fields', 'site_contacts'])->get()->toArray();
        $this->assertEqualsFixture('simpro_site_create_or_update_event_fixture.json', $simproSite);

        $jobs = Job::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('jobs_create_or_update_event_fixture.json', $jobs);
    }

    public function testDeleteQuoteEvent()
    {
        $this->createSimproJob('simpro_webhook_quote_deleted_fixture.json');

        $this->artisan('simpro:handle-jobs')->assertExitCode(0);

        $simproJob = SimproJob::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_jobs_fixture.json', $simproJob);

        $quotes = Quote::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('quote_delete_event_fixture.json', $quotes);
    }

    public function testCreateRequest()
    {
        $this->mockCreateQuoteRequest();

        $response = $this->actingAs($this->user)->json('post', '/quotes/create-in-simpro', [
            'simpro_site_id' => 1,
            'type' => 1,
            'description' => 'Test quote...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testCreateRequestNoPermissionsForRequest()
    {
        $response = $this->actingAs($this->userWithoutPermissions)->json('post', '/quotes/create-in-simpro', [
            'simpro_site_id' => 2,
            'type' => 1,
            'description' => 'Test quote...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testCreateRequestByAdmin()
    {
        $this->mockCreateQuoteRequest();

        $response = $this->actingAs($this->admin)->json('post', '/quotes/create-in-simpro', [
            'simpro_site_id' => 2,
            'type' => 2,
            'description' => 'Test quote...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testCreateRequestNoPermissions()
    {
        $response = $this->actingAs($this->user)->json('post', '/quotes/create-in-simpro', [
            'simpro_site_id' => 2,
            'type' => 1,
            'description' => 'Test quote...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testCreateRequestSiteNotExists()
    {
        $response = $this->actingAs($this->admin)->json('post', '/quotes/create-in-simpro', [
            'simpro_site_id' => 0,
            'type' => 1,
            'description' => 'Test quote...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testCreateRequestNoAuth()
    {
        $response = $this->json('post', '/quotes/create-in-simpro', [
            'simpro_site_id' => 1,
            'type' => 1,
            'description' => 'Test quote...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function getSearchFilters()
    {
        return [
            [
                'filter' => ['all' => 1],
                'result' => 'search_by_all_quotes.json'
            ],
            [
                'filter' => [
                    'page' => 1,
                    'per_page' => 2,
                ],
                'result' => 'search_by_page_per_page_quotes.json'
            ],
            [
                'filter' => [
                    'order_by' => 'job_id',
                    'desc' => true,
                    'date_issued' => '2021-04-05',
                    'date_expiry' => '2021-05-05',
                    'note_query' => 'note',
                    'query' => 'Desc',
                    'name_query' => 'Name',
                    'simpro_job_id' => 100,
                    'quote_id' => 52648,
                    'business_groups' => ['Maintenance']
                ],
                'result' => 'search_quotes_complex.json'
            ],
        ];
    }

    /**
     * @dataProvider  getSearchFilters
     *
     * @param  array $filter
     * @param  string $fixture
     */
    public function testSearch($filter, $fixture)
    {
        $response = $this->actingAs($this->user)->json('get', '/quotes', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture($fixture, $response->json());
    }

    /**
     * @dataProvider  getSearchFilters
     *
     * @param  array $filter
     * @param  string $fixture
     */
    public function testSearchNoPermission($filter)
    {
        $response = $this->actingAs($this->userWithoutPermissions)->json('get', '/quotes', $filter);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @dataProvider  getSearchFilters
     *
     * @param  array $filter
     * @param  string $fixture
     */
    public function testSearchByAdmin($filter, $fixture)
    {
        $response = $this->actingAs($this->admin)->json('get', '/quotes', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture("admin_{$fixture}", $response->json());
    }

    public function testApproveQuote()
    {
        $this->mockApproveQuote();

        $response = $this->actingAs($this->user)->json('put', '/quotes/2/approve', [
            'order_no' => '100',
            'note' => 'Some note...'
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $quote = Quote::orderBy('id')->where('id', 2)->get()->toArray();
        $this->assertEqualsFixture('approve_quote_fixture.json', $quote);
    }

    public function testApproveQuoteByAdmin()
    {
        $this->mockApproveQuote();

        $response = $this->actingAs($this->admin)->json('put', '/quotes/6/approve', [
            'order_no' => '100',
            'note' => 'Some note...'
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $quote = Quote::orderBy('id')->where('id', 6)->get()->toArray();
        $this->assertEqualsFixture('approve_quote_by_admin_fixture.json', $quote);
    }

    public function testApproveQuoteNoPermission()
    {
        $response = $this->actingAs($this->userOnlyView)->json('put', '/quotes/6/approve');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testApproveQuoteNotExists()
    {
        $response = $this->actingAs($this->user)->json('put', '/quotes/0/approve');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testApproveQuoteIncorrectStage()
    {
        $response = $this->actingAs($this->user)->json('put', '/quotes/1/approve');

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testApproveQuoteNoAuth()
    {
        $response = $this->json('put', '/quotes/2/approve');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDeclineQuote()
    {
        $this->mockDeclineQuote();

        $response = $this->actingAs($this->user)->json('put', '/quotes/3/decline', [
            'reason' => 'Some reason...'
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $quote = Quote::orderBy('id')->where('id', 3)->get()->toArray();
        $this->assertEqualsFixture('decline_quote_fixture.json', $quote);
    }

    public function testDeclineQuoteByAdmin()
    {
        $this->mockDeclineQuote();

        $response = $this->actingAs($this->admin)->json('put', '/quotes/6/decline', [
            'reason' => 'Some reason...'
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $quote = Quote::orderBy('id')->where('id', 6)->get()->toArray();
        $this->assertEqualsFixture('decline_quote_by_admin_fixture.json', $quote);
    }

    public function testDeclineQuoteNoPermission()
    {
        $response = $this->actingAs($this->userOnlyView)->json('put', '/quotes/6/decline');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testDeclineQuoteNotExists()
    {
        $response = $this->actingAs($this->user)->json('put', '/quotes/0/decline');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDeclineQuoteIncorrectStatus()
    {
        $response = $this->actingAs($this->user)->json('put', '/quotes/1/decline');

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testDeclineQuoteNoAuth()
    {
        $response = $this->json('put', '/quotes/2/decline');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testReRequestQuote()
    {
        $this->mockReRequestQuote();

        $response = $this->actingAs($this->user)->json('put', '/quotes/2/re-request', [
            'reason' => 'Some reason...'
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $quote = Quote::orderBy('id')->where('id', 2)->get()->toArray();
        $this->assertEqualsFixture('re-request_quote_fixture.json', $quote);
    }

    public function testReRequestQuoteByAdmin()
    {
        $this->mockReRequestQuote();

        $response = $this->actingAs($this->admin)->json('put', '/quotes/7/re-request', [
            'reason' => 'Some reason...'
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $quote = Quote::orderBy('id')->where('id', 7)->get()->toArray();
        $this->assertEqualsFixture('re-request_quote_by_admin_fixture.json', $quote);
    }

    public function testReRequestQuoteExpiredDate()
    {
        $this->mockReRequestQuote();

        $response = $this->actingAs($this->user)->json('put', '/quotes/4/re-request');

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $quote = Quote::orderBy('id')->where('id', 4)->get()->toArray();
        $this->assertEqualsFixture('re-request_quote_expired_date_fixture.json', $quote);
    }

    public function testReRequestQuoteNoPermission()
    {
        $response = $this->actingAs($this->userOnlyView)->json('put', '/quotes/7/re-request');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testReRequestQuoteNotExists()
    {
        $response = $this->actingAs($this->user)->json('put', '/quotes/0/re-request');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testReRequestQuoteIncorrectStatus()
    {
        $response = $this->actingAs($this->user)->json('put', '/quotes/1/re-request');

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testReRequestQuoteWrongStatusId()
    {
        $response = $this->actingAs($this->user)->json('put', '/quotes/10/re-request');

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testReRequestQuoteNoAuth()
    {
        $response = $this->json('put', '/quotes/2/re-request');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDownloadQuoteAttachment()
    {
        $this->mockDownloadQuoteNoteAttachment();

        $response = $this->actingAs($this->user)->json('get', '/quotes/1/download');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testDownloadQuoteAttachmentNotExists()
    {
        $response = $this->actingAs($this->user)->json('get', '/quotes/0/download');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDownloadQuoteAttachmentBadRequest()
    {
        $response = $this->actingAs($this->user)->json('get', '/quotes/2/download');

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testDownloadQuoteAttachmentNoPermissions()
    {
        $response = $this->actingAs($this->userWithoutPermissions)->json('get', '/quotes/2/download');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testDownloadQuoteAttachmentNoAuth()
    {
        $response = $this->json('get', '/quotes/1/download');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
