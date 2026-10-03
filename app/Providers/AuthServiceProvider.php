<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\CandidateDocument;
use App\Policies\ApplicationPolicy;
use App\Policies\CandidatePolicy;
use App\Policies\DocumentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Candidate::class => CandidatePolicy::class,
        Application::class => ApplicationPolicy::class,
        CandidateDocument::class => DocumentPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
