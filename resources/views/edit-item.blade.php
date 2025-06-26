<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Update Item - <em>{{ $item->name }}</em>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 text-gray-900">
                @if($errors->any())
                    {{ implode('', $errors->all('<div>:message</div>')) }}
                @endif
                <form method="post" action="{{ route('items.update', $item->id) }}" class="bg-white py-8 px-6 shadow rounded-lg mb-0 space-y-5" enctype="multipart/form-data">
                    @csrf
                    <!-- <div class="space-y-5 md:flex md:space-x-2 md:space-y-0">
                        <div>
                            <label for="first-name" class="block text-sm font-medium text-gray-700">First Name</label>
                            <div class="mt-1">
                                <input type="text" name="first-name" id="first-name" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded shadow-sm focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500" required>
                            </div>
                        </div>
                        
                        <div>
                            <label for="last-name" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <div class="mt-1">
                                <input type="text" name="last-name" id="last-name" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded shadow-sm focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500" required>
                            </div>
                        </div>
                    </div> -->
                    
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Item Name</label>
                        <div class="mt-1">
                            <input type="text" name="name" id="name" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded shadow-sm focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500" required
                            value="{{ old('name') ?? $item->name }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                        <input type="number" id="price" name="price" 
                        placeholder="Price"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required
                        value="{{ old('price') ?? $item->price }}">
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea type="text" id="description" name="description" 
                        placeholder="Item Description"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('description') ?? $item->description }}</textarea>
                    </div>
                    
                    <div>
                        <label for="imageUpload" class="block text-sm font-medium text-gray-700">Image</label>
                        <div class="mt-1">
                            <input type="file" name="image" id="imageUpload"  accept="image/png, image/gif, image/jpeg" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded shadow-sm focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500">
                        </div>
                    </div>
                    
                    
                    
                    <button type="submit" class="w-full px-4 py-2 text-center bg-green-500 rounded border border-transparent shadow-sm text-white font-medium hover:bg-green-600 focus:outline-none focus:ring-1 focus:ring-green-400" >
                        Update Item
                    </button>
                    
                </form>
            </div>
        </div>
    </div>


    @section('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>  
        <script>            
            $(document).ready(() => {
                const maxSize = 2 * 1024 * 1024;

                $("#imageUpload").on("change", function (e) {

                    // Get the selected file
                    const file = this.files[0];

                    // Check if file size exceeds 2MB
                    if (file && file.size > maxSize) {
                        alert('File size exceeds the 2MB limit.')
                        // Clear the file input field to prevent file from being uploaded
                        $(this).val('');
                    } else {
                        $('#error-message').hide();
                    }

                });


            });
        </script>
    @endsection
</x-app-layout>