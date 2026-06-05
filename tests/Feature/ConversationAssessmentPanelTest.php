<?php

namespace Tests\Feature;

use App\Models\ConversationAssessment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationAssessmentPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite extension is required for database feature tests.');
        }

        parent::setUp();
    }

    public function test_teacher_can_update_existing_conversation_assessment(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);
        $student = Student::query()->create([
            'student_no' => 'S-001',
            'class_group' => 'A',
            'school_year' => '2025/2026',
            'full_name' => 'Budi Santoso',
        ]);

        $assessment = ConversationAssessment::query()->create([
            'student_id' => $student->id,
            'assessed_on' => '2026-06-05',
            'activity' => 'Kegiatan awal',
            'aspect' => 'Aspek awal',
            'score_label' => 'MB',
        ]);

        $response = $this->actingAs($teacher)->post(route('guru.panel.store'), [
            'assessment_id' => $assessment->id,
            'student_id' => $student->id,
            'assessed_on' => '2026-06-05',
            'activity' => 'Mengenal binatang',
            'aspect' => 'Menjawab pertanyaan secara lisan',
            'score_label' => 'BSH',
        ]);

        $response
            ->assertRedirect(route('guru.panel', ['date' => '2026-06-05', 'group' => 'A']))
            ->assertSessionHas('success', 'Penilaian percakapan berhasil diperbarui.');

        $this->assertDatabaseHas('conversation_assessments', [
            'id' => $assessment->id,
            'student_id' => $student->id,
            'activity' => 'Mengenal binatang',
            'aspect' => 'Menjawab pertanyaan secara lisan',
            'score_label' => 'BSH',
        ]);
    }
}
