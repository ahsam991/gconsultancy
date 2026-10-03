<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Models\Course;
use App\Models\CourseRequirement;
use App\Models\DocumentType;
use App\Models\University;
use App\Services\AuditService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with('university');
        if ($s = $request->get('q')) {
            $query->where('name', 'like', "%{$s}%");
        }
        if ($request->filled('university_id')) {
            $query->where('university_id', $request->get('university_id'));
        }
        $courses = $query->orderBy('name')->paginate(15)->withQueryString();
        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        $universities = University::where('active', true)->orderBy('name')->get();
        return view('courses.create', compact('universities'));
    }

    public function store(StoreCourseRequest $request)
    {
        $course = Course::create($request->validated());
        AuditService::log('course.created', $course, null, $course->toArray());
        return redirect()->route('courses.show', $course)->with('status', 'Course created.');
    }

    public function show(Course $course)
    {
        $course->load('university');
        return view('courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $universities = University::where('active', true)->orderBy('name')->get();
        return view('courses.edit', compact('course', 'universities'));
    }

    public function update(StoreCourseRequest $request, Course $course)
    {
        $old = $course->toArray();
        $course->update($request->validated());
        AuditService::log('course.updated', $course, $old, $course->fresh()->toArray());
        return redirect()->route('courses.show', $course)->with('status', 'Course updated.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        AuditService::log('course.deleted', $course);
        return redirect()->route('courses.index')->with('status', 'Course deleted.');
    }

    public function finder(Request $request)
    {
        $query = Course::with('university')->where('active', true);
        if ($request->filled('subject')) {
            $query->where('name', 'like', '%'.$request->get('subject').'%');
        }
        if ($request->filled('level')) {
            $query->where('study_level_id', $request->get('level'));
        }
        if ($request->filled('country')) {
            $query->whereHas('university', fn ($q) => $q->where('country_id', $request->get('country')));
        }
        if ($request->filled('max_fee')) {
            $query->where('tuition_fee', '<=', $request->get('max_fee'));
        }
        if ($request->filled('ielts')) {
            $query->where(function ($q) use ($request) {
                $q->whereNull('ielts_req')->orWhere('ielts_req', '<=', $request->get('ielts'));
            });
        }
        $courses = $query->orderBy('tuition_fee')->paginate(15)->withQueryString();
        if ($request->wantsJson()) {
            return response()->json($courses);
        }
        return view('courses.finder', compact('courses'));
    }

    /* ------------------------- requirements (nested) ------------------------- */

    public function requirementsIndex(Course $course)
    {
        $requirements = CourseRequirement::with('documentType')
            ->where('course_id', $course->id)
            ->orderBy('id')
            ->paginate(15);
        $documentTypes = DocumentType::where('active', true)->orderBy('name')->get(['id', 'name']);
        $course->load('university');
        return view('courses.requirements', compact('course', 'requirements', 'documentTypes'));
    }

    public function requirementStore(Request $request, Course $course)
    {
        $data = $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'required' => 'nullable|boolean',
            'note' => 'nullable|string|max:1000',
        ]);
        $data['course_id'] = $course->id;
        $data['required'] = $request->boolean('required', true);

        $existing = CourseRequirement::where('course_id', $course->id)
            ->where('document_type_id', $data['document_type_id'])
            ->first();

        if ($existing) {
            $existing->update(['required' => $data['required'], 'note' => $data['note'] ?? $existing->note]);
            return back()->with('status', 'Requirement updated.');
        }

        CourseRequirement::create($data);
        return back()->with('status', 'Requirement added.');
    }

    public function requirementDestroy(Course $course, CourseRequirement $requirement)
    {
        abort_if($requirement->course_id !== $course->id, 404);
        $requirement->delete();
        return back()->with('status', 'Requirement removed.');
    }
}
