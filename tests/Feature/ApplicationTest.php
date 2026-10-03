<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApplicationTest extends TestCase
{
    /**
     * No Application Eloquent model exists yet in this repo (contract lists
     * Application/Candidate models as backend-provided but they are absent).
     * These tests lock the transition contract in config/consultancy.php so
     * the backend model can enforce exactly this map. They stay green and
     * independent of seeders.
     */
    protected function transitions(): array
    {
        return config('consultancy.application_status_transitions', []);
    }

    protected function applyTransition(array $history, string $from, string $to): array
    {
        $map = $this->transitions();

        if (! in_array($to, $map[$from] ?? [], true)) {
            throw new \InvalidArgumentException("Illegal transition: {$from} -> {$to}");
        }

        $history[] = ['from' => $from, 'to' => $to, 'at' => now()->toDateTimeString()];

        return [$to, $history];
    }

    public function test_legal_status_transition_creates_history(): void
    {
        $history = [['from' => null, 'to' => 'draft', 'at' => now()->toDateTimeString()]];

        [$status, $history] = $this->applyTransition($history, 'draft', 'submitted');
        $this->assertEquals('submitted', $status);

        [$status, $history] = $this->applyTransition($history, 'submitted', 'under_review');
        $this->assertEquals('under_review', $status);

        $this->assertCount(3, $history);
        $this->assertEquals('under_review', end($history)['to']);
    }

    public function test_illegal_transition_is_rejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->applyTransition([], 'draft', 'visa_granted');
    }

    public function test_terminal_states_have_no_outgoing_transitions(): void
    {
        $map = $this->transitions();

        foreach (['enrolled', 'withdrawn', 'rejected'] as $terminal) {
            $this->assertSame([], $map[$terminal] ?? null, "{$terminal} must be terminal");
        }
    }

    public function test_full_happy_path_is_legal(): void
    {
        $path = [
            ['draft', 'submitted'],
            ['submitted', 'under_review'],
            ['under_review', 'offer_unconditional'],
            ['offer_unconditional', 'deposit_paid'],
            ['deposit_paid', 'cas_issued'],
            ['cas_issued', 'visa_lodged'],
            ['visa_lodged', 'visa_granted'],
            ['visa_granted', 'enrolled'],
        ];

        $history = [];
        foreach ($path as [$from, $to]) {
            [$status, $history] = $this->applyTransition($history, $from, $to);
            $this->assertEquals($to, $status);
        }

        $this->assertCount(count($path), $history);
    }
}
