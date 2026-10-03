<nav class="sidebar overflow-auto" id="sidebarNav">
    <div class="p-3 border-bottom">
        <a href="{{ Route::has('dashboard') ? route('dashboard') : url('/') }}" class="gc-side-brand d-flex align-items-center gap-2 fw-bold">
            <span class="gc-crest" style="width:30px;height:30px;font-size:.85rem"><i class="fa-solid fa-building-flag"></i></span>
            <span class="small">Global Consultancy<span class="d-block fw-normal" style="font-size:.68rem;letter-spacing:.12em;color:#8b96b3">EDUCATION DESK</span></span>
        </a>
    </div>
    <div class="p-2 flex-grow-1">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('dashboard*') ? 'active' : '' }}" href="{{ Route::has('dashboard') ? route('dashboard') : url('/') }}"><i class="fa-solid fa-house me-2"></i>Dashboard</a></li>
            @can('candidates.view')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#sbCandidates"><i class="fa-solid fa-users me-2"></i>Candidates</a>
                <ul id="sbCandidates" class="collapse nav-small">
                    @if(Route::has('candidates.index'))<li><a class="nav-link" href="{{ route('candidates.index') }}">All Candidates</a></li>@endif
                    @if(Route::has('candidates.create'))<li><a class="nav-link" href="{{ route('candidates.create') }}">Add Candidate</a></li>@endif
                    @if(Route::has('leads.index'))<li><a class="nav-link" href="{{ route('leads.index') }}">Leads</a></li>@endif
                </ul>
            </li>
            @endcan
            @can('applications.view')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#sbApps"><i class="fa-solid fa-file-lines me-2"></i>Applications</a>
                <ul id="sbApps" class="collapse nav-small">
                    @if(Route::has('applications.index'))<li><a class="nav-link" href="{{ route('applications.index') }}">All Applications</a></li>@endif
                    @if(Route::has('applications.create'))<li><a class="nav-link" href="{{ route('applications.create') }}">New Application</a></li>@endif
                </ul>
            </li>
            @endcan
            @can('documents.view')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#sbDocs"><i class="fa-solid fa-folder-open me-2"></i>Documents</a>
                <ul id="sbDocs" class="collapse nav-small">
                    @if(Route::has('documents.index'))<li><a class="nav-link" href="{{ route('documents.index') }}">Manage Documents</a></li>@endif
                    @if(Route::has('documents.create'))<li><a class="nav-link" href="{{ route('documents.create') }}">Upload</a></li>@endif
                </ul>
            </li>
            @endcan
            @can('universities.view')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#sbUni"><i class="fa-solid fa-building-columns me-2"></i>Universities</a>
                <ul id="sbUni" class="collapse nav-small">
                    @if(Route::has('universities.index'))<li><a class="nav-link" href="{{ route('universities.index') }}">Universities</a></li>@endif
                    @if(Route::has('courses.index'))<li><a class="nav-link" href="{{ route('courses.index') }}">Courses</a></li>@endif
                    @if(Route::has('courses.finder'))<li><a class="nav-link" href="{{ route('courses.finder') }}">Course Finder</a></li>@endif
                </ul>
            </li>
            @endcan
            @can('offers.view')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#sbJourney"><i class="fa-solid fa-route me-2"></i>Student Journey</a>
                <ul id="sbJourney" class="collapse nav-small">
                    @if(Route::has('offers.index'))<li><a class="nav-link" href="{{ route('offers.index') }}">Offers</a></li>@endif
                    @if(Route::has('cas.index'))<li><a class="nav-link" href="{{ route('cas.index') }}">CAS Records</a></li>@endif
                    @if(Route::has('visa.index'))<li><a class="nav-link" href="{{ route('visa.index') }}">Visa Cases</a></li>@endif
                    @if(Route::has('enrolments.index'))<li><a class="nav-link" href="{{ route('enrolments.index') }}">Enrolments</a></li>@endif
                </ul>
            </li>
            @endcan
            @can('tasks.view')
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}" href="{{ Route::has('tasks.index') ? route('tasks.index') : '#' }}"><i class="fa-solid fa-list-check me-2"></i>Tasks</a></li>
            @endcan
            @can('appointments.view')
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}" href="{{ Route::has('appointments.index') ? route('appointments.index') : '#' }}"><i class="fa-solid fa-calendar-days me-2"></i>Appointments</a></li>
            @endcan
            @can('candidates.view')
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('counselling.*') ? 'active' : '' }}" href="{{ Route::has('counselling.index') ? route('counselling.index') : '#' }}"><i class="fa-solid fa-comments me-2"></i>Counselling</a></li>
            @endcan
            @can('tasks.view')
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}" href="{{ Route::has('messages.index') ? route('messages.index') : '#' }}"><i class="fa-solid fa-envelope me-2"></i>Messages</a></li>
            @endcan
            @can('appointments.view')
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}" href="{{ Route::has('calendar.index') ? route('calendar.index') : '#' }}"><i class="fa-solid fa-calendar-week me-2"></i>Calendar</a></li>
            @endcan
            @can('tasks.view')
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('automation.*') ? 'active' : '' }}" href="{{ Route::has('automation.rules') ? route('automation.rules') : '#' }}"><i class="fa-solid fa-robot me-2"></i>Automation</a></li>
            @endcan
            @can('commissions.view')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#sbFin"><i class="fa-solid fa-coins me-2"></i>Finance</a>
                <ul id="sbFin" class="collapse nav-small">
                    @if(Route::has('commissions.index'))<li><a class="nav-link" href="{{ route('commissions.index') }}">Commissions</a></li>@endif
                    @if(Route::has('invoices.index'))<li><a class="nav-link" href="{{ route('invoices.index') }}">Invoices</a></li>@endif
                </ul>
            </li>
            @endcan
            @can('reports.view')
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ Route::has('reports.index') ? route('reports.index') : '#' }}"><i class="fa-solid fa-chart-bar me-2"></i>Reports</a></li>
            @endcan
            @can('cms.view')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#sbCms"><i class="fa-solid fa-globe me-2"></i>CMS</a>
                <ul id="sbCms" class="collapse nav-small">
                    @if(Route::has('cms.pages.index'))<li><a class="nav-link" href="{{ route('cms.pages.index') }}">Pages</a></li>@endif
                    @if(Route::has('cms.testimonials.index'))<li><a class="nav-link" href="{{ route('cms.testimonials.index') }}">Testimonials</a></li>@endif
                    @if(Route::has('cms.team.index'))<li><a class="nav-link" href="{{ route('cms.team.index') }}">Team</a></li>@endif
                    @if(Route::has('cms.faqs.index'))<li><a class="nav-link" href="{{ route('cms.faqs.index') }}">FAQs</a></li>@endif
                </ul>
            </li>
            @endcan
            @can('settings.view')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#sbSet"><i class="fa-solid fa-gear me-2"></i>Admin</a>
                <ul id="sbSet" class="collapse nav-small">
                    @if(Route::has('settings.index'))<li><a class="nav-link" href="{{ route('settings.index') }}">Settings</a></li>@endif
                    @if(Route::has('users.index'))<li><a class="nav-link" href="{{ route('users.index') }}">Users</a></li>@endif
                    @if(Route::has('teams.index'))<li><a class="nav-link" href="{{ route('teams.index') }}">Teams</a></li>@endif
                    @if(Route::has('audit.index'))<li><a class="nav-link" href="{{ route('audit.index') }}">Audit Log</a></li>@endif
                    @if(Route::has('settings.sessions'))<li><a class="nav-link" href="{{ route('settings.sessions') }}">Sessions</a></li>@endif
                    @if(Route::has('settings.login-history'))<li><a class="nav-link" href="{{ route('settings.login-history') }}">Login History</a></li>@endif
                    @if(Route::has('notifications.index'))<li><a class="nav-link" href="{{ route('notifications.index') }}">Notifications</a></li>@endif
                    @if(Route::has('email-templates.index'))<li><a class="nav-link" href="{{ route('email-templates.index') }}">Email Templates</a></li>@endif
                    @if(Route::has('branches.index'))<li><a class="nav-link" href="{{ route('branches.index') }}">Branches</a></li>@endif
                    @if(Route::has('gdpr.requests'))<li><a class="nav-link" href="{{ route('gdpr.requests') }}">GDPR</a></li>@endif
                    @if(Route::has('workflow.templates'))<li><a class="nav-link" href="{{ route('workflow.templates') }}">Workflows</a></li>@endif
                    @if(Route::has('system.health'))<li><a class="nav-link" href="{{ route('system.health') }}">System Health</a></li>@endif
                    @if(Route::has('system.backups'))<li><a class="nav-link" href="{{ route('system.backups') }}">Backups</a></li>@endif
                </ul>
            </li>
            @endcan
        </ul>
    </div>
    <div class="p-3 border-top small text-muted">{{ auth()->user()->name ?? 'User' }}<span class="d-block">v1.0</span></div>
</nav>
