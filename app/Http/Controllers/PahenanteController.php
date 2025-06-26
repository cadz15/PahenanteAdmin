<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
                        <a href="' . $editUrl . '" class="text-orange-600" title="edit"><i class="fa-solid  fa-pen-to-square"></i></a>
                        
                        <button type="submit" class="text-red-600 delete-item" data-item='. $row->id .' title="delete"><i class="fa-solid fa-trash-can"></i></button>
                        
                    ';
                    return $actionBtn;
                })
                ->rawColumns(['action', 'image'])
                ->make(true);
    }

    public function storeSupplier(Request $request)
    {
        $validated = $request->validate([
            'supplier' => [
            'required'
        ],
            'image' => ['sometimes', 'image', 'mimes:jpg,jpeg,png', 'max:2048']
        ]);

        $supplier = Supplier::create([
            'supplier' => $request->input('supplier'),
            'image' => ''
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Store image in 'public/images' directory and get the path
            $image = $request->file('image');
            $imageName = 'supplier-'. $supplier->id . '.'. $image->getClientOriginalExtension();
            
            $imagePath = $image->storeAs('uploads', $imageName, 'public');
            
            $supplier->update([
                'image' => $imageName ?? $supplier->image,
            ]);
        }

        return redirect(route('dashboard'));
    }

    public function updateSupplier(Request $request, $id)
    {
        $validated = $request->validate([
            'supplier' => [
            'required', 
            Rule::unique('suppliers', 'supplier')->ignore($id)
        ],
            'image' => ['sometimes', 'image', 'mimes:jpg,jpeg,png', 'max:2048']
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Store image in 'public/images' directory and get the path
            $image = $request->file('image');
            $imageName = 'supplier-'. $id . '.'. $image->getClientOriginalExtension();
            
            $imagePath = $image->storeAs('uploads', $imageName, 'public');
        }

        // Create the supplier record (assuming you have a Supplier model)
       $supplier = Supplier::where('id', $id)->first();
       
       if(empty($supplier)) abort(404);

       $supplier->update([
            'supplier' => $request->input('supplier'),
            'image' => $imageName ?? $supplier->image,
        ]);

        return redirect(route('dashboard'));
    }


    public function destroySupplier($id)
    {
        $supplier = Supplier::findOrFail($id);

        $ids = Item::where('supplier_id', $id)->pluck('id')->toArray();

        Item::destroy($ids);

        $supplier->delete();

        return response()->json([
            'message' => 'success',
        ], 200);
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

        $data = Item::where('supplier_id', $id)->latest()->get();
        
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('image', function($row) {
                // Assuming the image URL is stored in $row->image
                $imageUrl = route('get-file', $row->image); // Modify the path based on your actual image storage location
                return '<img src="' . $imageUrl . '" alt="Image" width="50" height="50" />';
            })
            ->addColumn('action', function($row){
                $editUrl = route('items.edit', $row->id);
                $deleteUrl = route('items.destroy', $row->id); // typically used via AJAX or form method spoofing

                $actionBtn = '
                    <a href="' . $editUrl . '" class="text-orange-600" ti title="edit"><i class="fa-solid  fa-pen-to-square"></i></a>
                    
                    <button class="text-red-600 delete-item" data-item="'. $row->id .'" title="delete"><i class="fa-solid fa-trash-can"></i></button>
                   
                ';
                return $actionBtn;
            })
            ->rawColumns(['action', 'image'])
            ->make(true);
    }

    public function itemStore(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => [
            'required'
        ],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'price' => ['required', 'min:0'],
            'description' => ['required']
        ]);

         // Create the supplier record (assuming you have a Supplier model)
       $item = Item::create([
            'name' => $request->input('name'),
            'supplier_id' => $id,
            'image' => '',
            'price' => $request->input('price'),
            'description' => $request->input('description')
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Store image in 'public/images' directory and get the path
            $image = $request->file('image');
            $imageName = 'item-'. $id . '-' . $item->id .'.'. $image->getClientOriginalExtension();
            
            $imagePath = $image->storeAs('uploads', $imageName, 'public');
        }

        $item->update([
            'image' => $imageName ?? '-'
        ]);
       

        return redirect(route('item.list', $id));
    }


    public function itemEdit($id)
    {
        $item = Item::findOrFail($id);

        return view('edit-item', compact('item'));
    }


    public function itemUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => [
            'required',            
        ],
            'image' => ['sometimes', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'price' => ['required', 'min:0'],
            'description' => ['required']
        ]);

       $item = Item::findOrFail($id);

    
        // Handle image upload
        if ($request->hasFile('image')) {
            // Store image in 'public/images' directory and get the path
            $image = $request->file('image');
            $imageName = 'item-'. $id . '-' . $item->id .'.'. $image->getClientOriginalExtension();
            
            $imagePath = $image->storeAs('uploads', $imageName, 'public');

        }
        
        $item->update([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
            'image' => $imageName ?? $item->image
        ]);
        

        return redirect(route('item.list', $item->supplier_id));
    }


    public function itemDelete($id)
    {
        $item = Item::findOrFail($id);

        $path = storage_path('app/public/uploads/' . $item->image);

        if (file_exists($path)) {
            Storage::delete($path);
        }

        $item->delete();

        return response()->json([
            'message' => 'success',
        ], 200);
    }


    public function qrUpdate()
    {
       $ip = gethostbyname(gethostname());

       return view('qr-update', compact('ip'));
    }


    public function mobileUpdate($mobileId)
    {
        if($mobileId != 'ph62525') return response()->json([
            'message' => 'Error!'
        ], 404);

        $suppliers = Supplier::all()->toJson();
        $items = Item::all()->toJson();
        $ip = gethostbyname(gethostname());


        return response()->json([
            'ip' => $ip,
            'suppliers' => $suppliers,
            'items' => $items
        ], 200);
    }
}
