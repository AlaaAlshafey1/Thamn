<?php
namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereHas('role',function($query){

            $query->where('name',"!=",'superadmin');
        })->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::where("name","!=","superadmin")->get();
        return view('users.create', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $name = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/users'), $name);
            $data['image'] = $name;
        }

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        if ($request->role_id) {
            $user->assignRole(Role::find($request->role_id)->name);
        }

        return redirect()->route('users.index')->with('success', 'تم إضافة المستخدم بنجاح');
    }

    public function edit(User $user)
    {
        $roles = Role::where("name","!=","superadmin")->get();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        // ✅ لو فيه صورة جديدة فقط
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // احذف القديمة إن وجدت
            if ($user->image && file_exists(public_path('uploads/users/' . $user->image))) {
                unlink(public_path('uploads/users/' . $user->image));
            }

            $file = $request->file('image');
            $name = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/users'), $name);
            $data['image'] = $name;
        }

        // ✅ لو كلمة المرور فاضية متحدثهاش
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        // ✅ تحديث الدور فقط لو اتغير
        if ($request->role_id) {
            $user->syncRoles(Role::find($request->role_id)->name);
        }

        return redirect()->route('users.index')->with('success', 'تم تحديث المستخدم بنجاح');
    }

    public function destroy(User $user)
    {
        if ($user->image && file_exists(public_path('uploads/users/' . $user->image))) {
            unlink(public_path('uploads/users/' . $user->image));
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'تم حذف المستخدم بنجاح');
    }
}
