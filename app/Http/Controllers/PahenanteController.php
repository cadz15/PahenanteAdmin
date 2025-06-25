<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class PahenanteController extends Controller
{
    //
    public function showUpdateSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);

        return view('create-supplier', compact('id', 'supplier'));
    }

    public function supplierList()
    {
        $data = Supplier::latest()->withCount('items')->get();
        
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function($row) {
                    // Assuming the image URL is stored in $row->image
                    $imageUrl = route('get-file', $row->image); // Modify the path based on your actual image storage location
                    return '<img src="' . $imageUrl . '" alt="Image" width="50" height="50" />';
                })
                ->addColumn('items', function($row) {
                    return $row->items_count;
                })
                ->addColumn('action', function($row){
                    $viewUrl = route('item.list', $row->id);
                    $editUrl = route('supplier.edit', $row->id);
                    $deleteUrl = route('supplier.destroy', $row->id); // typically used via AJAX or form method spoofing

                    $actionBtn = '
                        <a href="' . $viewUrl . '" class="text-blue-600"  title="view"><i class="fa-solid fa-eye"></i></a>
                        <a href="' . $editUrl . '" class="text-orange-600" ti title="edit"><i class="fa-solid  fa-pen-to-square"></i></a>
                        <form action="' . $deleteUrl . '" method="POST" style="display:inline-block;" onsubmit="return confirm(\'Are you sure?\')">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="text-red-600" title="delete"><i class="fa-solid fa-trash-can"></i></button>
                        </form>
                    ';
                    return $actionBtn;
                })
                ->rawColumns(['action', 'image'])
                ->make(true);
    }

    public function updateSupplier(Request $request, $id)
    {
        $validated = $request->validate([
            'supplier' => [
            'required', 
            Rule::unique('suppliers', 'supplier')->ignore($id)
        ],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048']
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Store image in 'public/images' directory and get the path
            $image = $request->file('image');
            $imageName = 'supplier-'. $id . '.'. $image->getClientOriginalExtension();
            
            $imagePath = $image->storeAs('uploads', $imageName, 'public');
        }

        // Create the supplier record (assuming you have a Supplier model)
       $supplier = Supplier::where('id', $id)->first()
       ?->update([
            'supplier' => $request->input('supplier'),
            'image' => $imageName ?? null,
        ]);

        return redirect(route('dashboard'));
    }


    public function destroySupplier($id)
    {
        $supplier = Supplier::findOrFail($id);

        $supplier->delete();

        return redirect(route('dashboard'));
    }


    public function itemList($id)
    {

        $supplier = Supplier::findOrFail($id);
        
        return view('item-list', compact('supplier'));
    }



    public function getFile($filename)
    {
        $path = storage_path('app/public/uploads/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }


    public function items($id)
    {

        $data = Item::where('id', $id)->latest()->get();
        
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('image', function($row) {
                // Assuming the image URL is stored in $row->image
                $imageUrl = route('get-file', $row->image); // Modify the path based on your actual image storage location
                return '<img src="' . $imageUrl . '" alt="Image" width="50" height="50" />';
            })
            ->addColumn('action', function($row){
                $viewUrl = route('item.list', $row->id);
                $editUrl = route('supplier.edit', $row->id);
                $deleteUrl = route('supplier.destroy', $row->id); // typically used via AJAX or form method spoofing

                $actionBtn = '
                    <a href="' . $viewUrl . '" class="text-blue-600"  title="view"><i class="fa-solid fa-eye"></i></a>
                    <a href="' . $editUrl . '" class="text-orange-600" ti title="edit"><i class="fa-solid  fa-pen-to-square"></i></a>
                    <form action="' . $deleteUrl . '" method="POST" style="display:inline-block;" onsubmit="return confirm(\'Are you sure?\')">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="text-red-600" title="delete"><i class="fa-solid fa-trash-can"></i></button>
                    </form>
                ';
                return $actionBtn;
            })
            ->rawColumns(['action', 'image'])
            ->make(true);
    }
}
