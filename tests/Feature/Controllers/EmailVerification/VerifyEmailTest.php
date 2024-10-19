<?php

namespace Tests\Feature\Controllers\EmailVerification;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class VerifyEmailTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_verify_email_successful()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $url = URL::signedRoute('verification.verify', ['id' => $user->id, 'hash' => "validHash"]);

        $response = $this->get($url);

        $user->refresh();

        $this->assertNotNull($user->email_verified_at);
        $response->assertStatus(302);
        $response->assertRedirect('https://event-planner-orcin.vercel.app/email-verification?message=Email+verified+successfully.');
    }

    public function test_verify_email_failure_if_invalid_signature()
    {
        $user = User::factory()->create();

        $url = route('verification.verify', ['id' => $user->id, 'hash' => 'invalidHash']);

        $response = $this->get($url);

        $response->assertStatus(302);
        $response->assertRedirect('https://event-planner-orcin.vercel.app/email-verification?message=Invalid+URL+provided.');
    }

    public function test_verify_email_failure_if_user_not_found()
    {
        $invalidUserId = 99999;

        $url = URL::signedRoute('verification.verify', ['id' => $invalidUserId, 'hash' => 'someHash']);

        $response = $this->get($url);

        $response->assertStatus(302);
        $response->assertRedirect('https://event-planner-orcin.vercel.app/email-verification?message=User+not+found.');
    }
}
