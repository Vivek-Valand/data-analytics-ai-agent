<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('email_templates')->insert([
            [
                'key' => 'verification_email',
                'subject' => 'Verify Your Email Address - ##app_name##',
                'email_content' => '
                    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; rounded: 12px;">
                        <h2 style="color: #4f46e5;">Welcome, ##username##!</h2>
                        <p style="color: #4b5563; font-size: 16px; line-height: 1.5;">
                            Thank you for joining <strong>##app_name##</strong>. To complete your registration and start using our Data Analytics Agent, please verify your email address by clicking the button below:
                        </p>
                        <div style="text-align: center; margin: 30px 0;">
                            <a href="##verification_url##" style="background-color: #4f46e5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;">Verify Email Address</a>
                        </div>
                        <p style="color: #9ca3af; font-size: 14px;">
                            If you did not create an account, no further action is required.
                        </p>
                    </div>
                ',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
