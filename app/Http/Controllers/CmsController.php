<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\WebsitePage;
use App\Services\AuditService;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    public function pagesIndex()
    {
        $pages = WebsitePage::orderBy('slug')->paginate(15);
        return view('cms.pages.index', compact('pages'));
    }

    public function pageCreate()
    {
        return view('cms.pages.create');
    }

    public function pageStore(Request $request)
    {
        $data = $request->validate([
            'slug' => 'required|string|max:255|unique:website_pages,slug',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'template' => 'nullable|string|max:100',
            'active' => 'nullable|boolean',
        ]);
        $page = WebsitePage::create($data);
        AuditService::log('cms.page.created', null, null, $page->toArray());
        return redirect()->route('cms.pages.edit', $page)->with('status', 'Page created.');
    }

    public function pageShow(WebsitePage $page)
    {
        return redirect()->route('cms.pages.edit', $page);
    }

    public function pageEdit(WebsitePage $page)
    {
        return view('cms.pages.edit', compact('page'));
    }

    public function pageUpdate(Request $request, WebsitePage $page)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'template' => 'nullable|string|max:100',
            'active' => 'nullable|boolean',
        ]);
        $page->update($data);
        return redirect()->route('cms.pages.edit', $page)->with('status', 'Page updated.');
    }

    public function pageDestroy(WebsitePage $page)
    {
        $page->delete();
        return redirect()->route('cms.pages.index')->with('status', 'Page deleted.');
    }

    public function testimonialsIndex()
    {
        $testimonials = Testimonial::orderByDesc('created_at')->paginate(15);
        return view('cms.testimonials.index', compact('testimonials'));
    }

    public function testimonialCreate()
    {
        return view('cms.testimonials.create');
    }

    public function testimonialStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'content' => 'required|string',
            'rating' => 'required|numeric|min:1|max:5',
            'is_featured' => 'nullable|boolean',
        ]);
        $t = Testimonial::create($data);
        return redirect()->route('cms.testimonials.edit', $t)->with('status', 'Testimonial created.');
    }

    public function testimonialShow(Testimonial $testimonial)
    {
        return redirect()->route('cms.testimonials.edit', $testimonial);
    }

    public function testimonialEdit(Testimonial $testimonial)
    {
        return view('cms.testimonials.edit', compact('testimonial'));
    }

    public function testimonialUpdate(Request $request, Testimonial $testimonial)
    {
        $testimonial->update($request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'content' => 'required|string',
            'rating' => 'required|numeric|min:1|max:5',
            'is_featured' => 'nullable|boolean',
        ]));
        return redirect()->route('cms.testimonials.edit', $testimonial)->with('status', 'Testimonial updated.');
    }

    public function testimonialDestroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return redirect()->route('cms.testimonials.index')->with('status', 'Testimonial deleted.');
    }

    public function teamIndex()
    {
        $team = TeamMember::orderBy('name')->paginate(15);
        return view('cms.team.index', compact('team'));
    }

    public function teamCreate()
    {
        return view('cms.team.create');
    }

    public function teamStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'linkedin_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'is_active' => 'nullable|boolean',
        ]);
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('team', 'public');
        }
        $member = TeamMember::create($data);
        return redirect()->route('cms.team.edit', $member)->with('status', 'Team member created.');
    }

    public function teamShow(TeamMember $team)
    {
        return redirect()->route('cms.team.edit', $team);
    }

    public function teamEdit(TeamMember $team)
    {
        return view('cms.team.edit', ['member' => $team]);
    }

    public function teamUpdate(Request $request, TeamMember $team)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'linkedin_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'is_active' => 'nullable|boolean',
        ]);
        $team->update($data);
        return redirect()->route('cms.team.edit', $team)->with('status', 'Team member updated.');
    }

    public function teamDestroy(TeamMember $team)
    {
        $team->delete();
        return redirect()->route('cms.team.index')->with('status', 'Team member deleted.');
    }

    public function faqsIndex()
    {
        $faqs = Faq::orderBy('sort_order')->paginate(15);
        return view('cms.faqs.index', compact('faqs'));
    }

    public function faqCreate()
    {
        return view('cms.faqs.create');
    }

    public function faqStore(Request $request)
    {
        $data = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:100',
            'active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);
        $faq = Faq::create($data);
        return redirect()->route('cms.faqs.edit', $faq)->with('status', 'FAQ created.');
    }

    public function faqShow(Faq $faq)
    {
        return redirect()->route('cms.faqs.edit', $faq);
    }

    public function faqEdit(Faq $faq)
    {
        return view('cms.faqs.edit', compact('faq'));
    }

    public function faqUpdate(Request $request, Faq $faq)
    {
        $faq->update($request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:100',
            'active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]));
        return redirect()->route('cms.faqs.edit', $faq)->with('status', 'FAQ updated.');
    }

    public function faqDestroy(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('cms.faqs.index')->with('status', 'FAQ deleted.');
    }
}
