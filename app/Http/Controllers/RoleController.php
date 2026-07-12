<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        $title = 'Role';
        $search = request()->input('search');

        $roles = Role::when($search, function ($query) use ($search) {
            return $query->where('name', 'LIKE', '%'.$search.'%');
        })
        ->orderBy('name', 'ASC')
        ->paginate(10)
        ->withQueryString();

        return view('role.index', compact(
            'title',
            'roles',
        ));
    }

    public function tambah()
    {
        $title = 'Tambah Role';
        
        // Group permissions by their prefix (e.g. "absen.create" -> ABSEN)
        $permissions = Permission::all()->groupBy(function($item) {
            $parts = explode('.', $item->name);
            return strtoupper($parts[0]);
        })->sortKeys();

        return view('role.tambah', compact(
            'title',
            'permissions',
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:roles,name',
            'guard_name' => 'required',
            'permissions' => 'nullable|array'
        ]);

        DB::transaction(function() use ($validated, $request) {
            $role = Role::create([
                'name' => $validated['name'],
                'guard_name' => $validated['guard_name']
            ]);

            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }
        });

        return redirect('/role')->with('success', 'Data Berhasil Disimpan');
    }

    public function edit($id)
    {
        $title = 'Edit Role';
        $role = Role::findOrFail($id);
        
        $permissions = Permission::all()->groupBy(function($item) {
            $parts = explode('.', $item->name);
            return strtoupper($parts[0]);
        })->sortKeys();

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('role.edit', compact(
            'title',
            'role',
            'permissions',
            'rolePermissions'
        ));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'guard_name' => 'required',
            'permissions' => 'nullable|array'
        ]);

        DB::transaction(function() use ($role, $validated, $request) {
            $role->update([
                'name' => $validated['name'],
                'guard_name' => $validated['guard_name']
            ]);

            $permissions = $request->input('permissions', []);
            $role->syncPermissions($permissions);
        });

        return redirect('/role')->with('success', 'Data Berhasil Diupdate');
    }

    public function delete($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return redirect('/role')->with('success', 'Data Berhasil Dihapus');
    }
}
