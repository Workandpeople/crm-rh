<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Company, User, Role};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::with(['admin:id,first_name,last_name,email'])
            ->orderBy('name')
            ->get([
                'id','name','domain','email','phone','address','logo_path',
                'admin_user_id','created_at','deleted_at'
            ]);

        return response()->json($companies);
    }

    public function options()
    {
        $adminRoleIds = Role::whereIn('name', ['admin','superadmin'])->pluck('id');

        $admins = User::whereIn('role_id', $adminRoleIds)
            ->orderBy('last_name')
            ->get(['id','first_name','last_name','email']);

        return response()->json(['admins' => $admins]);
    }

    public function show(Company $company)
    {
        return response()->json(
            $company->load(['admin:id,first_name,last_name,email'])
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required','string','max:190'],
            'domain'        => ['required','string','max:190','unique:companies,domain'],
            'email'         => ['nullable','email','max:190'],
            'phone'         => ['nullable','string','max:50'],
            'address'       => ['nullable','string','max:255'],
            'admin_user_id' => ['nullable','uuid', Rule::exists('users','id')],
            'logo'          => ['nullable','image','mimes:jpeg,jpg,png,webp','max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $this->processLogo($request->file('logo'));
        }

        $company = Company::create($validated);

        return response()->json(['message' => 'Société créée', 'company' => $company], 201);
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name'          => ['required','string','max:190'],
            'domain'        => ['required','string','max:190', Rule::unique('companies','domain')->ignore($company->id)],
            'email'         => ['nullable','email','max:190'],
            'phone'         => ['nullable','string','max:50'],
            'address'       => ['nullable','string','max:255'],
            'admin_user_id' => ['nullable','uuid', Rule::exists('users','id')],
            'logo'          => ['nullable','image','mimes:jpeg,jpg,png,webp','max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $this->processLogo($request->file('logo'));
        }

        $company->update($validated);

        return response()->json(['message' => 'Société mise à jour', 'company' => $company]);
    }

    public function destroy(Company $company)
    {
        $company->delete(); // soft delete
        return response()->json(['message' => 'Société supprimée']);
    }

    private function processLogo(?\Illuminate\Http\UploadedFile $file): ?string
    {
        if (!$file) return null;

        // 2 Mo max déjà validé par la règle
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getPathname());

        // Redimension douce si trop grand
        $maxW = 800;
        $maxH = 800;
        if ($image->width() > $maxW || $image->height() > $maxH) {
            $image = $image->scaleDown($maxW, $maxH);
        }

        // Encodage WebP
        $encoded = $image->toWebp(85);

        $path = 'companies/'.Str::uuid().'.webp';
        Storage::disk('public')->put($path, (string) $encoded);

        // Retourne chemin public accessible via /storage
        return 'storage/'.$path;
    }
}
