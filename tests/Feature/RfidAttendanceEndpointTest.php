<?php

namespace Tests\Feature;

use Tests\TestCase;

class RfidAttendanceEndpointTest extends TestCase
{
    public function test_rfid_attendance_requires_rfid_code(): void
    {
        config(['services.rfid_attendance.token' => null]);

        $response = $this->postJson('/api/rfid/attendance', []);

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_rfid_attendance_rejects_invalid_device_token(): void
    {
        config(['services.rfid_attendance.token' => 'secret-token']);

        $response = $this->postJson('/api/rfid/attendance', [
            'rfid_code' => '04A1B2C3',
            'token' => 'wrong-token',
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);
    }
}
