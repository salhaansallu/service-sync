<?php

namespace Tests\Feature;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RepairsController;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AccountsSummaryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'app.key' => 'base64:'.base64_encode(str_repeat('x', 32)),
        ]);
        DB::purge('sqlite');
        Http::preventStrayRequests();
        Carbon::setTestNow('2026-10-21 12:00:00');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });
        Schema::create('pos_users', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('pos_code');
        });
        Schema::create('pos_data', function (Blueprint $table) {
            $table->id();
            $table->string('pos_code');
            $table->integer('admin_id')->default(1);
            $table->integer('plan')->default(2);
        });
        Schema::create('user_data', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
        });
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
        });
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
        });
        foreach (['2023_11_29_113658_create_credits_table.php',
            '2025_04_13_115720_create_expenses_table.php',
            '2025_05_08_234521_create_employee_expenses_table.php'] as $migration) {
            (require database_path('migrations/'.$migration))->up();
        }
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->string('bill_no');
            $table->string('pos_code');
            $table->string('type');
            $table->string('status');
            $table->string('model_no')->default('Test TV');
            $table->string('techie')->nullable();
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('advance', 12, 2)->default(0);
            $table->decimal('cost', 12, 2)->default(0);
            $table->decimal('commission', 12, 2)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
        DB::table('pos_data')->insert(['pos_code' => 'TEST']);
        DB::table('pos_users')->insert(['user_id' => 1, 'pos_code' => 'TEST']);
        DB::table('users')->insert(['id' => 1, 'type' => 'tv-repairs', 'fname' => 'Test', 'lname' => 'Technician', 'email' => 'test@example.com', 'created_at' => now()]);
        $user = User::findOrFail(1);
        $this->actingAs($user);
        $this->app['request']->cookies->set('pos_session', Crypt::encrypt('TEST'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function repair(int $id, array $attributes = []): void
    {
        DB::table('repairs')->insert(array_merge([
            'id' => $id, 'bill_no' => (string) $id, 'pos_code' => 'TEST',
            'type' => 'repair', 'status' => 'Repaired', 'total' => 100,
            'advance' => 10, 'cost' => 20, 'commission' => 5,
            'paid_at' => null, 'created_at' => '2026-10-20 10:00:00',
            'updated_at' => '2026-10-21 10:00:00',
        ], $attributes));
    }

    private function summary(array $filters = []): array
    {
        return (new DashboardController())->accounts(
            Request::create('/dashboard/accounts/repair', 'GET', $filters), 'repair'
        )->getData();
    }

    public function test_unpaid_bills_are_visible_but_excluded_from_paid_totals(): void
    {
        $this->repair(1, ['paid_at' => '2026-10-21 11:00:00']);
        $this->repair(2, ['total' => 200, 'cost' => 50]);
        $this->repair(3, ['pos_code' => 'OTHER', 'paid_at' => '2026-10-21 11:00:00']);
        $this->repair(4, ['paid_at' => '2026-10-19 11:00:00']);
        $this->repair(5, ['status' => 'Pending']);
        $this->repair(6, ['type' => 'other', 'total' => 80, 'paid_at' => '2026-10-21 11:00:00']);
        DB::table('credits')->insert(['customer_id' => 1, 'ammount' => 50, 'pos_code' => 'TEST', 'order_id' => '2']);

        $data = $this->summary();
        $this->assertSame([1], $data['repairs']->pluck('id')->all());
        $this->assertSame([2, 1], $data['displayRepairs']->pluck('id')->all());
        $this->assertEquals(90, $data['repairs']->sum('finaltotal'));
        $this->assertEquals(20, $data['spareCost']);
        $this->assertEquals(5, $data['commission']);
        $this->assertEquals(50, $data['displayRepairs']->firstWhere('id', 2)->creditAmount);
        $this->assertEquals(90, $data['tvRepairs']->sum('finaltotal'));
        $this->assertEquals(70, $data['otherRepairs']->sum('finaltotal'));
    }

    public function test_payment_toggle_moves_bill_into_and_out_of_totals(): void
    {
        $this->repair(1);
        $controller = new RepairsController();
        $response = $controller->markPaid(Request::create('/dashboard/repairs/mark-paid', 'POST', ['modelid' => 1, 'status' => 'paid']));
        $this->assertEquals(0, json_decode($response->getContent())->error);
        $this->assertEquals(90, $this->summary()['repairs']->sum('finaltotal'));

        $controller->markPaid(Request::create('/dashboard/repairs/mark-paid', 'POST', ['modelid' => 1, 'status' => 'unpaid']));
        $data = $this->summary();
        $this->assertCount(0, $data['repairs']);
        $this->assertCount(1, $data['displayRepairs']);
        $this->assertEquals(0, $data['spareCost']);
    }

    public function test_payment_toggle_cannot_modify_another_company(): void
    {
        $this->repair(1, ['pos_code' => 'OTHER']);
        $response = (new RepairsController())->markPaid(Request::create('/dashboard/repairs/mark-paid', 'POST', ['modelid' => 1, 'status' => 'paid']));
        $this->assertEquals(1, json_decode($response->getContent())->error);
        $this->assertNull(DB::table('repairs')->where('id', 1)->value('paid_at'));
    }

    public function test_blank_dates_default_to_today(): void
    {
        $this->repair(1, ['paid_at' => '2026-10-21 11:00:00']);
        $this->repair(2, ['paid_at' => '2026-10-19 11:00:00']);
        foreach ([null, ''] as $blank) {
            $data = $this->summary(['fromdate' => $blank, 'todate' => $blank]);
            $this->assertSame([1], $data['repairs']->pluck('id')->all());
            $this->assertSame('2026-10-21', $data['fromdate']);
            $this->assertSame('2026-10-21', $data['todate']);
        }
    }

    public function test_date_range_includes_the_entire_start_and_end_days(): void
    {
        $this->repair(1, ['paid_at' => '2026-10-19 00:00:00']);
        $this->repair(2, ['paid_at' => '2026-10-20 23:59:59']);
        $this->repair(3, ['paid_at' => '2026-10-21 00:00:00']);
        $this->repair(4, ['updated_at' => '2026-10-20 23:59:59']);
        $data = $this->summary(['fromdate' => '2026-10-19', 'todate' => '2026-10-20']);
        $this->assertSame([1, 2], $data['repairs']->pluck('id')->all());
        $this->assertSame([4, 2, 1], $data['displayRepairs']->pluck('id')->all());
    }

    public function test_invalid_payment_status_does_not_change_the_bill(): void
    {
        $this->repair(1, ['paid_at' => '2026-10-20 11:00:00']);
        $response = (new RepairsController())->markPaid(Request::create('/dashboard/repairs/mark-paid', 'POST', ['modelid' => 1, 'status' => 'invalid']));
        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame('2026-10-20 11:00:00', DB::table('repairs')->where('id', 1)->value('paid_at'));
    }

    public function test_reversed_date_range_is_rejected(): void
    {
        $this->expectException(ValidationException::class);
        $this->summary(['fromdate' => '2026-10-22', 'todate' => '2026-10-21']);
    }

    public function test_accounts_page_renders_and_payment_actions_work_through_http(): void
    {
        $this->withoutVite();
        $this->repair(1, ['paid_at' => '2026-10-21 11:00:00', 'techie' => '1']);
        $this->repair(2, ['techie' => '1']);
        $this->withCookie('pos_session', Crypt::encrypt('TEST'))->withCredentials();

        $this->get('/dashboard/accounts/repair')->assertOk()
            ->assertSee('Accounts Summery')
            ->assertSee('data-id="1" data-status="unpaid"', false)
            ->assertSee('data-id="2" data-status="paid"', false);
        $this->postJson('/dashboard/repairs/mark-paid', ['modelid' => 2, 'status' => 'paid'])
            ->assertOk()->assertJson(['error' => 0]);
        $this->get('/dashboard/accounts/repair')->assertOk()
            ->assertSee('data-id="2" data-status="unpaid"', false);
        $this->postJson('/dashboard/repairs/mark-paid', ['modelid' => 2, 'status' => 'unpaid'])
            ->assertOk()->assertJson(['error' => 0]);
        $this->get('/dashboard/accounts/repair')->assertOk()
            ->assertSee('data-id="2" data-status="paid"', false);
    }
}
