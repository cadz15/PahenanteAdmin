<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PahenanteController extends Controller
{
    //
    public function createSupplier()
    {
        return view('create-supplier');
    }

    public function supplierList()
    {
        $data = Supplier::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $viewUrl = route('item.list', $row->id);
                    $editUrl = route('supplier.edit', $row->id);
                    $deleteUrl = route('supplier.destroy', $row->id); // typically used via AJAX or form method spoofing

                    $actionBtn = '
                        <a href="' . $viewUrl . '" class="btn btn-primary btn-sm">View</a>
                        <a href="' . $editUrl . '" class="btn btn-success btn-sm">Edit</a>
                        <form action="' . $deleteUrl . '" method="POST" style="display:inline-block;" onsubmit="return confirm(\'Are you sure?\')">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    ';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
    }

    public function storeSupplier(Request $request)
    {
        $validated = $request->validate([
            'supplier' => ['required', 'unique:suppliers,supplier'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048']
        ]);


        // Handle image upload
        if ($request->hasFile('image')) {
            // Store image in 'public/images' directory and get the path
            $imagePath = $request->file('image')->store('images', 'public');
        }

        // Create the supplier record (assuming you have a Supplier model)
       $supplier = Supplier::create([
            'supplier' => $request->input('supplier'),
            'image' => $imagePath ?? null,
        ]);

        return redirect(route('item.list', $supplier->id));
    }



    public function itemList($id)
    {

        $supplier = Supplier::findOrFail($id);
        
        return view('item-list', compact('supplier'));
    }
}
