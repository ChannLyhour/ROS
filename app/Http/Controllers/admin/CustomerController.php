<?php

namespace App\Http\Controllers\admin;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\UploadImageHelper;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Customer::class);
        $query = Customer::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $customers = $query->paginate(10);

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Customer::class);
        return view('admin.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Customer::class);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = UploadImageHelper::store($request->file('image'), 'customers');

        Customer::create($request->except('image') + ['image' => $image]);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $this->authorize('view', $customer);
        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $this->authorize('update', $customer);
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $this->authorize('update', $customer);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = $customer->image;
        if ($request->hasFile('image')) {
            $image = UploadImageHelper::store($request->file('image'), 'customers', 'public', $customer->image);
        }

        $customer->update(array_merge($request->except('image'), ['image' => $image]));

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $this->authorize('delete', $customer);
        if ($customer->image) {
            UploadImageHelper::delete($customer->image);
        }
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully!');
    }
}
