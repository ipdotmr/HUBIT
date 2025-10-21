<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportDepartment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupportDepartmentController extends Controller
{
    public function index()
    {
        $departments = SupportDepartment::orderBy('sort_order')
            ->orderBy('name_en')
            ->get();

        return Inertia::render('Admin/SupportDepartments/Index', [
            'departments' => $departments,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/SupportDepartments/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'name_fr' => 'required|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'description_fr' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'required|integer|min:0',
        ]);

        SupportDepartment::create($validated);

        return redirect()->route('managit.support-departments.index')
            ->with('success', 'Support department created successfully');
    }

    public function edit($id)
    {
        $department = SupportDepartment::findOrFail($id);

        return Inertia::render('Admin/SupportDepartments/Edit', [
            'department' => $department,
        ]);
    }

    public function update(Request $request, $id)
    {
        $department = SupportDepartment::findOrFail($id);

        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'name_fr' => 'required|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'description_fr' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'required|integer|min:0',
        ]);

        $department->update($validated);

        return redirect()->route('managit.support-departments.index')
            ->with('success', 'Support department updated successfully');
    }

    public function destroy($id)
    {
        $department = SupportDepartment::findOrFail($id);

        if ($department->tickets()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete department with existing tickets');
        }

        $department->delete();

        return redirect()->route('managit.support-departments.index')
            ->with('success', 'Support department deleted successfully');
    }
}
