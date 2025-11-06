<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Tour;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class PaymentReminderTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates test bookings for payment reminder testing
     */
    public function run(): void
    {
        $this->command->info('🌱 Creating test bookings for payment reminders...');

        // Get or create a test tour
        $tour = Tour::first();
        if (!$tour) {
            $this->command->error('❌ No tours found. Please seed tours first with: php artisan db:seed --class=TourSeeder');
            return;
        }

        // Get an existing customer
        $customer = Customer::first();
        if (!$customer) {
            $this->command->error('❌ No customers found. Please seed customers first.');
            return;
        }

        $testCases = [
            // 7-day reminder scenarios
            [
                'label' => '7-day reminder (needs balance)',
                'start_date' => now()->addDays(7),
                'payment_status' => 'deposit_paid',
                'amount_remaining' => 1500.00,
                'status' => 'confirmed',
            ],
            [
                'label' => '7-day reminder (already sent)',
                'start_date' => now()->addDays(7),
                'payment_status' => 'deposit_paid',
                'amount_remaining' => 1200.00,
                'status' => 'confirmed',
                'reminder_7days_sent_at' => now()->subDays(1),
            ],

            // 3-day reminder scenarios
            [
                'label' => '3-day reminder (needs balance)',
                'start_date' => now()->addDays(3),
                'payment_status' => 'deposit_paid',
                'amount_remaining' => 800.00,
                'status' => 'confirmed',
            ],
            [
                'label' => '3-day reminder (7-day sent)',
                'start_date' => now()->addDays(3),
                'payment_status' => 'deposit_paid',
                'amount_remaining' => 950.00,
                'status' => 'confirmed',
                'reminder_7days_sent_at' => now()->subDays(4),
            ],

            // 1-day reminder scenarios
            [
                'label' => '1-day reminder (urgent)',
                'start_date' => now()->addDays(1),
                'payment_status' => 'deposit_paid',
                'amount_remaining' => 600.00,
                'status' => 'confirmed',
            ],
            [
                'label' => '1-day reminder (previous sent)',
                'start_date' => now()->addDays(1),
                'payment_status' => 'deposit_paid',
                'amount_remaining' => 700.00,
                'status' => 'confirmed',
                'reminder_7days_sent_at' => now()->subDays(6),
                'reminder_3days_sent_at' => now()->subDays(2),
            ],

            // Edge cases - should NOT send reminders
            [
                'label' => 'Paid in full (no reminder)',
                'start_date' => now()->addDays(7),
                'payment_status' => 'paid_in_full',
                'amount_remaining' => 0.00,
                'status' => 'confirmed',
            ],
            [
                'label' => 'Cancelled booking (no reminder)',
                'start_date' => now()->addDays(7),
                'payment_status' => 'deposit_paid',
                'amount_remaining' => 500.00,
                'status' => 'cancelled',
            ],
            [
                'label' => 'Tour already started (no reminder)',
                'start_date' => now()->subDays(1),
                'payment_status' => 'deposit_paid',
                'amount_remaining' => 400.00,
                'status' => 'confirmed',
            ],
        ];

        $created = 0;

        foreach ($testCases as $testCase) {
            $booking = Booking::create([
                'tour_id' => $tour->id,
                'customer_id' => $customer->id,
                'customer_name' => $testCase['label'],
                'customer_email' => 'test+' . \Str::slug($testCase['label']) . '@example.com',
                'customer_phone' => '+998901234567',
                'pax_total' => 2,
                'start_date' => $testCase['start_date'],
                'end_date' => $testCase['start_date']->copy()->addDays($tour->duration ?? 1),
                'total_price' => 2000.00,
                'amount_paid' => 2000.00 - $testCase['amount_remaining'],
                'amount_remaining' => $testCase['amount_remaining'],
                'payment_status' => $testCase['payment_status'],
                'status' => $testCase['status'],
                'currency' => 'USD',
                'booking_type' => 'group',
                'reminder_7days_sent_at' => $testCase['reminder_7days_sent_at'] ?? null,
                'reminder_3days_sent_at' => $testCase['reminder_3days_sent_at'] ?? null,
                'reminder_1day_sent_at' => $testCase['reminder_1day_sent_at'] ?? null,
            ]);

            $this->command->line("   ✓ Created: {$testCase['label']} (Booking #{$booking->id})");
            $created++;
        }

        $this->command->newLine();
        $this->command->info("✅ Created {$created} test bookings");
        $this->command->line('💡 Test with: php artisan reminders:payment --dry-run');
        $this->command->line('💡 Test specific: php artisan reminders:payment --dry-run --days=7');
    }
}
