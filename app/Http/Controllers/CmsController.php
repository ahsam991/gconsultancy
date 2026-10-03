<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\MediaLibrary;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\WebsitePage;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    /* ------------------------------------------------------------------
     | Banners
     | ------------------------------------------------------------------ */

    public function bannersIndex()
    {
        $banners = Banner::orderBy('sort_order')->orderByDesc('created_at')->paginate(15);
        return view('cms.banners.index', compact('banners'));
    }

    public function bannerCreate()
    {
        return view('cms.banners.create');
    }

    public function bannerStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'link' => 'nullable|string|max:500',
            'location' => 'required|string|max:100',
            'active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('banners', 'public');
        }
        $data['active'] = $request->boolean('active', true);
        $banner = Banner::create($data);
        return redirect()->route('cms.banners.edit', $banner)->with('status', 'Banner created.');
    }

    public function bannerShow(Banner $banner)
    {
        return redirect()->route('cms.banners.edit', $banner);
    }

    public function bannerEdit(Banner $banner)
    {
        return view('cms.banners.edit', compact('banner'));
    }

    public function bannerUpdate(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'link' => 'nullable|string|max:500',
            'location' => 'required|string|max:100',
            'active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $data['image'] = $request->file('image')->store('banners', 'public');
        } else {
            unset($data['image']);
        }
        $data['active'] = $request->boolean('active');
        $banner->update($data);
        return redirect()->route('cms.banners.edit', $banner)->with('status', 'Banner updated.');
    }

    public function bannerDestroy(Banner $banner)
    {
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }
        $banner->delete();
        return redirect()->route('cms.banners.index')->with('status', 'Banner deleted.');
    }

    /* ------------------------------------------------------------------
     | Menus + items
     | ------------------------------------------------------------------ */

    public function menusIndex()
    {
        $menus = Menu::with(['items' => fn ($q) => $q->orderBy('sort_order')])->orderBy('name')->paginate(15);
        return view('cms.menus.index', compact('menus'));
    }

    public function menuCreate()
    {
        return view('cms.menus.index', ['menus' => Menu::orderBy('name')->paginate(15), 'createMode' => true]);
    }

    public function menuStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:100',
            'active' => 'nullable|boolean',
        ]);
        $data['active'] = $request->boolean('active', true);
        $menu = Menu::create($data);
        return redirect()->route('cms.menus.items', $menu)->with('status', 'Menu created.');
    }

    public function menuShow(Menu $menu)
    {
        return redirect()->route('cms.menus.items', $menu);
    }

    public function menuEdit(Menu $menu)
    {
        $menus = Menu::orderBy('name')->paginate(15);
        return view('cms.menus.index', compact('menus', 'menu'));
    }

    public function menuUpdate(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:100',
            'active' => 'nullable|boolean',
        ]);
        $data['active'] = $request->boolean('active');
        $menu->update($data);
        return redirect()->route('cms.menus.items', $menu)->with('status', 'Menu updated.');
    }

    public function menuDestroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('cms.menus.index')->with('status', 'Menu deleted.');
    }

    public function menuItems(Menu $menu)
    {
        $menu->load(['items' => fn ($q) => $q->orderBy('sort_order')]);
        $parents = $menu->items()->whereNull('parent_id')->orderBy('sort_order')->get();
        return view('cms.menus.items', compact('menu', 'parents'));
    }

    public function menuItemStore(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'label' => 'required|string|max:255',
            'url' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:menu_items,id',
            'sort_order' => 'nullable|integer|min:0',
            'active' => 'nullable|boolean',
        ]);
        $data['menu_id'] = $menu->id;
        $data['active'] = $request->boolean('active', true);
        MenuItem::create($data);
        return back()->with('status', 'Menu item added.');
    }

    public function menuItemUpdate(Request $request, Menu $menu, MenuItem $item)
    {
        abort_if($item->menu_id !== $menu->id, 404);
        $data = $request->validate([
            'label' => 'required|string|max:255',
            'url' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:menu_items,id',
            'sort_order' => 'nullable|integer|min:0',
            'active' => 'nullable|boolean',
        ]);
        $data['active'] = $request->boolean('active');
        $item->update($data);
        return back()->with('status', 'Menu item updated.');
    }

    public function menuItemDestroy(Menu $menu, MenuItem $item)
    {
        abort_if($item->menu_id !== $menu->id, 404);
        $item->delete();
        return back()->with('status', 'Menu item deleted.');
    }

    /* ------------------------------------------------------------------
     | Blog categories + posts
     | ------------------------------------------------------------------ */

    public function blogCategoriesIndex()
    {
        $categories = BlogCategory::withCount('posts')->orderBy('name')->paginate(15);
        return view('cms.blog.categories', compact('categories'));
    }

    public function blogCategoryStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name',
            'slug' => 'nullable|string|max:255|unique:blog_categories,slug',
            'active' => 'nullable|boolean',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['active'] = $request->boolean('active', true);
        BlogCategory::create($data);
        return back()->with('status', 'Category created.');
    }

    public function blogCategoryUpdate(Request $request, BlogCategory $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name,'.$category->id,
            'slug' => 'nullable|string|max:255|unique:blog_categories,slug,'.$category->id,
            'active' => 'nullable|boolean',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['active'] = $request->boolean('active');
        $category->update($data);
        return back()->with('status', 'Category updated.');
    }

    public function blogCategoryDestroy(BlogCategory $category)
    {
        $category->delete();
        return back()->with('status', 'Category deleted.');
    }

    public function blogPostsIndex()
    {
        $posts = BlogPost::with('category')->orderByDesc('created_at')->paginate(15);
        return view('cms.blog.index', compact('posts'));
    }

    public function blogPostCreate()
    {
        $categories = BlogCategory::where('active', true)->orderBy('name')->get();
        return view('cms.blog.create', compact('categories'));
    }

    public function blogPostStore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'excerpt' => 'nullable|string|max:1000',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
        ]);
        $data['slug'] = $data['slug'] ?? $this->uniquePostSlug($data['title']);
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }
        $data['author_id'] = $request->user()?->id;
        $post = BlogPost::create($data);
        return redirect()->route('cms.blog.edit', $post)->with('status', 'Post created.');
    }

    public function blogPostShow(BlogPost $post)
    {
        return redirect()->route('cms.blog.edit', $post);
    }

    public function blogPostEdit(BlogPost $post)
    {
        $categories = BlogCategory::where('active', true)->orderBy('name')->get();
        return view('cms.blog.edit', compact('post', 'categories'));
    }

    public function blogPostUpdate(Request $request, BlogPost $post)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug,'.$post->id,
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'excerpt' => 'nullable|string|max:1000',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        } else {
            unset($data['featured_image']);
        }
        $post->update($data);
        return redirect()->route('cms.blog.edit', $post)->with('status', 'Post updated.');
    }

    public function blogPostDestroy(BlogPost $post)
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        $post->delete();
        return redirect()->route('cms.blog.index')->with('status', 'Post deleted.');
    }

    protected function uniquePostSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 2;
        while (BlogPost::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }
        return $slug;
    }

    /* ------------------------------------------------------------------
     | Forms + submissions
     | ------------------------------------------------------------------ */

    public function formsIndex()
    {
        $forms = Form::withCount('submissions')->orderBy('name')->paginate(15);
        return view('cms.forms.index', compact('forms'));
    }

    public function formCreate()
    {
        return view('cms.forms.create');
    }

    public function formStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:forms,slug',
            'fields' => 'required|json',
            'active' => 'nullable|boolean',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['fields'] = json_decode($data['fields'], true);
        $data['active'] = $request->boolean('active', true);
        $form = Form::create($data);
        return redirect()->route('cms.forms.edit', $form)->with('status', 'Form created.');
    }

    public function formShow(Form $form)
    {
        return redirect()->route('cms.forms.edit', $form);
    }

    public function formEdit(Form $form)
    {
        return view('cms.forms.edit', compact('form'));
    }

    public function formUpdate(Request $request, Form $form)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:forms,slug,'.$form->id,
            'fields' => 'required|json',
            'active' => 'nullable|boolean',
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['fields'] = json_decode($data['fields'], true);
        $data['active'] = $request->boolean('active');
        $form->update($data);
        return redirect()->route('cms.forms.edit', $form)->with('status', 'Form updated.');
    }

    public function formDestroy(Form $form)
    {
        $form->delete();
        return redirect()->route('cms.forms.index')->with('status', 'Form deleted.');
    }

    public function formSubmissions(Form $form)
    {
        $submissions = FormSubmission::where('form_id', $form->id)->orderByDesc('created_at')->paginate(15);
        return view('cms.forms.submissions', compact('form', 'submissions'));
    }

    public function formSubmissionShow(Form $form, FormSubmission $submission)
    {
        abort_if($submission->form_id !== $form->id, 404);
        $submissions = FormSubmission::where('form_id', $form->id)->orderByDesc('created_at')->paginate(15);
        return view('cms.forms.submissions', compact('form', 'submissions', 'submission'));
    }

    /* ------------------------------------------------------------------
     | Media library
     | ------------------------------------------------------------------ */

    public function mediaIndex(Request $request)
    {
        $query = MediaLibrary::orderByDesc('created_at');
        if ($s = $request->get('q')) {
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('alt', 'like', "%{$s}%");
            });
        }
        $media = $query->paginate(18)->withQueryString();
        return view('cms.media.index', compact('media'));
    }

    public function mediaStore(Request $request)
    {
        $data = $request->validate([
            'file' => 'required|file|max:2048',
            'alt' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
        ]);
        $file = $request->file('file');
        $path = $file->store('media', 'public');
        MediaLibrary::create([
            'name' => $data['name'] ?? $file->getClientOriginalName(),
            'path' => $path,
            'mime' => $file->getClientMimeType(),
            'size_kb' => (int) ceil($file->getSize() / 1024),
            'alt' => $data['alt'] ?? null,
            'uploaded_by' => $request->user()?->id,
        ]);
        return back()->with('status', 'File uploaded.');
    }

    public function mediaUpdate(Request $request, MediaLibrary $media)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'alt' => 'nullable|string|max:255',
        ]);
        $media->update($data);
        return back()->with('status', 'Media renamed.');
    }

    public function mediaDestroy(MediaLibrary $media)
    {
        Storage::disk('public')->delete($media->path);
        $media->delete();
        return back()->with('status', 'Media deleted.');
    }
}
