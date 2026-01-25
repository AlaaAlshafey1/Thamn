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
    $users = User::doesntHave('roles')->get();

    return view('users.index', compact('users'));
}
public function experts()
{
    $users = User::role('expert')
        ->withCount('expertOrders') // عدد الطلبات اللي قيمها
        ->get();

    return view('users.experts.index', compact('users'));
}


    public function showExpert(User $user)
    {
        if (!$user->hasRole('expert')) {
            return redirect()->back()->with('error', 'هذا المستخدم ليس خبيرًا');
        }

        $orders = $user->expertOrders()->get();
        $ordersCount = $orders->count();
        $totalEarned = $ordersCount * 4;

        // بيانات البنك (لو عندك في DB غيرها)
        $bank = [
            'bank_name' => $user->bank_name ?? 'مصرف الراجحي',
            'iban' => $user->iban ?? 'SA0000000000000000000000',
            'account_number' => $user->account_number ?? '1234567890',
            'account_name' => $user->first_name . ' ' . $user->last_name,
            'swift' => $user->swift ?? 'RJHISARI',
        ];

        return view('users.expert_show', compact('user', 'orders', 'ordersCount', 'totalEarned', 'bank'));
    }



    public function create()
    {
        $roles = Role::where("name","!=","superadmin")->get();
        return view('users.create', compact('roles'));
    }
    public function createExpert()
    {
        $roles = Role::where("name","!=","superadmin")->get();
        return view('users.experts.create', compact('roles'));
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
public function storeExpert(UserRequest $request)
{
    $data = $request->validated();

    // رفع الصورة
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $name = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/users'), $name);
        $data['image'] = $name;
    }

    // رفع شهادة الخبرة (لو موجودة)
    if ($request->hasFile('experience_certificate')) {
        $file = $request->file('experience_certificate');
        $name = time() . '_cert.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/experts/certificates'), $name);
        $data['experience_certificate'] = $name;
    }

    $data['password'] = Hash::make($data['password']);

    $user = User::create($data);

    // Assign role expert
    $user->assignRole('expert');

    return redirect()->route('experts.index')->with('success', 'تم إضافة الخبير بنجاح');
}
public function editExpert(User $user)
{
    // لو مش خبير
    if (!$user->hasRole('expert')) {
        return redirect()->back()->with('error', 'هذا المستخدم ليس خبيرًا');
    }

    $roles = Role::where("name","!=","superadmin")->get();
    return view('users.experts.edit', compact('user', 'roles'));
}
public function updateExpert(UserRequest $request, User $user)
{
    if (!$user->hasRole('expert')) {
        return redirect()->back()->with('error', 'هذا المستخدم ليس خبيرًا');
    }

    $data = $request->validated();

    if ($request->hasFile('image') && $request->file('image')->isValid()) {
        if ($user->image && file_exists(public_path('uploads/users/' . $user->image))) {
            unlink(public_path('uploads/users/' . $user->image));
        }
        $file = $request->file('image');
        $name = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/users'), $name);
        $data['image'] = $name;
    }

    if ($request->hasFile('experience_certificate')) {
        if ($user->experience_certificate && file_exists(public_path('uploads/experts/certificates/' . $user->experience_certificate))) {
            unlink(public_path('uploads/experts/certificates/' . $user->experience_certificate));
        }
        $file = $request->file('experience_certificate');
        $name = time() . '_cert.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/experts/certificates'), $name);
        $data['experience_certificate'] = $name;
    }

    if (!empty($data['password'])) {
        $data['password'] = Hash::make($data['password']);
    } else {
        unset($data['password']);
    }

    $user->update($data);

    $user->syncRoles(['expert']); // تأكد أنه خبير فقط

    return redirect()->route('experts.index')->with('success', 'تم تحديث بيانات الخبير بنجاح');
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

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

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
