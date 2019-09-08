<?php

namespace Tests\Feature\Http\Controllers\Web;

use Input;
use Tests\Feature\FunctionalTestCase;
use Vanguard\User;
use Carbon\Carbon;

class ActivityControllerTest extends FunctionalTestCase
{
    /**
     * @var \Vanguard\Services\Logging\UserActivity\Logger
     */
    public $logger;

    public function setUp()
    {
        parent::setUp();
        $this->logger = app(\Vanguard\Services\Logging\UserActivity\Logger::class);
    }

    public function test_display_all_activities()
    {
        $this->withoutMiddleware();

        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        Carbon::setTestNow(Carbon::now());
        $this->be($user1);
        $this->logger->log('foo');

        $this->be($user2);
        $this->logger->log('bar');

        $this->visit('activity');

        $this->assertEquals(2, $this->crawler->filter('table tbody tr')->count());

        $this->seeUserActivity($user1, 'foo', 1);
        $this->seeUserActivity($user2, 'bar', 2);
    }

    public function test_display_activities_for_user()
    {
        $user = $this->createAndLoginAdminUser();

        $this->logger->log('foo');

        $this->visit('activity')
            ->clickOn('table tbody a:first-child')
            ->seePageIs("activity/user/{$user->id}/log")
            ->seeUserActivity($user, 'foo', 1);
    }

    public function test_search_activities()
    {
        $this->withoutMiddleware();

        $user = factory(User::class)->create();
        $this->be($user);
        $this->logger->log('foo');
        $this->logger->log('bar');

        $this->visit('activity')
            ->type('fo', 'search')
            ->press('search-activities-btn');

        $this->seePageIs('activity?search=fo');
        $this->assertEquals(1, $this->crawler->filter('table tbody tr')->count());
        $this->seeUserActivity($user, 'foo', 1);
    }

    private function seeUserActivity($user, $message, $row)
    {
        return $this->seeInTable('table', $user->present()->nameOrEmail, $row, 1)
            ->seeInTable('table', Input::ip(), $row, 2)
            ->seeInTable('table', $message, $row, 3)
            ->seeInTable('table', Carbon::now()->format(config('app.date_time_format')), $row, 4)
            ->seeElement("table tbody tr:nth-child({$row}) > td:nth-child(5) > a[data-content='".Input::header('User-agent')."']");
    }

}
