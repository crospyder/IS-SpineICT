<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $this->ensureAdmin();

        $query = User::query();

        if ($q = request('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if (request('active') === '1') {
            $query->where('is_active', true);
        }

        if (request('active') === '0') {
            $query->where('is_active', false);
        }

        if (request('admin') === '1') {
            $query->where('is_admin', true);
        }

        if (request('admin') === '0') {
            $query->where('is_admin', false);
        }

        $users = $query
            ->orderBy('name')
            ->orderBy('email')
            ->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->ensureAdmin();

        return view('users.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $data = $this->validateUser($request);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => $request->boolean('is_admin'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('users.edit', $user)
            ->with('status', 'Korisnik je uspješno kreiran.');
    }

    public function edit(string $id)
    {
        $this->ensureAdmin();

        $user = User::findOrFail($id);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, string $id)
    {
        $this->ensureAdmin();

        $user = User::findOrFail($id);

        $data = $this->validateUser($request, $user);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'is_admin' => $request->boolean('is_admin'),
            'is_active' => $request->boolean('is_active', false),
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        if ($user->id === auth()->id() && !$updateData['is_active']) {
            return back()
                ->withErrors(['is_active' => 'Ne možeš deaktivirati vlastiti korisnički račun.'])
                ->withInput();
        }

        $user->update($updateData);

        return redirect()
            ->route('users.edit', $user)
            ->with('status', 'Korisnik je uspješno ažuriran.');
    }

    protected function validateUser(Request $request, ?User $user = null): array
    {
        $passwordRules = ['nullable', 'string', 'min:6'];

        if (!$user) {
            $passwordRules = ['required', 'string', 'min:6'];
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'password' => $passwordRules,
            'is_admin' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'email.unique' => 'Korisnik s tim emailom već postoji.',
            'password.required' => 'Lozinka je obavezna kod kreiranja korisnika.',
            'password.min' => 'Lozinka mora imati najmanje 6 znakova.',
        ]);
    }

    protected function ensureAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()?->is_admin, 403);
    }
}