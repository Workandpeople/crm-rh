<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::with([
            'author:id,first_name,last_name',
            'company:id,name',
        ])->orderByDesc('created_at');

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->query('company_id'));
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($request->filled('highlighted')) {
            $query->where('highlighted', filter_var($request->query('highlighted'), FILTER_VALIDATE_BOOLEAN));
        }

        return response()->json($query->get());
    }

    public function show(Blog $blog)
    {
        return response()->json(
            $blog->load([
                'author:id,first_name,last_name',
                'company:id,name',
            ])
        );
    }

    public function store(Request $request)
    {
        try {
            $validated = $this->validateData($request);
            $validated['user_id'] = Auth::id();
            $validated = $this->handleUploads($request, $validated);

            $blog = Blog::create($validated)->load(['author:id,first_name,last_name', 'company:id,name']);

            return response()->json([
                'message' => 'Article créé',
                'data' => $blog,
            ], 201);
        } catch (ValidationException $e) {
            Log::warning('Blog store validation failed', [
                'errors' => $e->errors(),
                'payload' => $request->except(['main_image','second_image','third_image','fourth_image']),
            ]);
            throw $e;
        }
    }

    public function update(Request $request, Blog $blog)
    {
        try {
            // Pré-remplit les champs requis si non envoyés (cas toggle rapide)
            $request->merge([
                'company_id' => $request->input('company_id', $blog->company_id),
                'title'      => $request->input('title', $blog->title),
                'status'     => $request->input('status', $blog->status),
                'highlighted'=> $request->input('highlighted', $blog->highlighted),
            ]);

            $validated = $this->validateData($request, $blog->id);
            $validated = $this->handleUploads($request, $validated, $blog);
            $blog->update($validated);

            return response()->json([
                'message' => 'Article mis à jour',
                'data' => $blog->fresh(['author:id,first_name,last_name', 'company:id,name']),
            ]);
        } catch (ValidationException $e) {
            Log::warning('Blog update validation failed', [
                'errors' => $e->errors(),
                'payload' => $request->except(['main_image','second_image','third_image','fourth_image']),
                'blog_id' => $blog->id,
            ]);
            throw $e;
        }
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
        return response()->json(['message' => 'Article supprimé']);
    }

    private function validateData(Request $request, ?string $blogId = null): array
    {
        // Cast / normalise
        $request->merge([
            'highlighted' => filter_var($request->input('highlighted'), FILTER_VALIDATE_BOOLEAN),
        ]);

        return $request->validate([
            'company_id' => ['required', 'uuid', Rule::exists('companies', 'id')],
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'highlighted' => ['boolean'],
            'main_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'main_image_credit' => ['nullable', 'string', 'max:255'],
            'second_title' => ['nullable', 'string', 'max:255'],
            'second_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'second_type' => ['nullable', Rule::in(['horizontal', 'vertical'])],
            'second_image_credit' => ['nullable', 'string', 'max:255'],
            'second_content' => ['nullable', 'string'],
            'third_content' => ['nullable', 'string'],
            'third_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'third_image_credit' => ['nullable', 'string', 'max:255'],
            'third_type' => ['nullable', Rule::in(['horizontal', 'vertical'])],
            'fourth_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'fourth_image_credit' => ['nullable', 'string', 'max:255'],
            'fourth_type' => ['nullable', Rule::in(['horizontal', 'vertical'])],
            'fourth_content' => ['nullable', 'string'],
        ]);
    }

    private function handleUploads(Request $request, array $validated, ?Blog $blog = null): array
    {
        foreach (['main_image', 'second_image', 'third_image', 'fourth_image'] as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('blogs', 'public');
                $validated[$field] = 'storage/'.$path;
            } elseif ($blog) {
                // keep existing path on update if no new file
                $validated[$field] = $blog->{$field};
            }
        }

        return $validated;
    }
}
