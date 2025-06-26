<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $supplier?->supplier }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <button id="modalBtn" class="py-2 px-4 inline-block bg-green-500 text-white hover:bg-green-800 rounded-sm mb-6">
                Add Item
            </button>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table id="item" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Item Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

     <!-- Modal -->
    <div id="itemModal" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-xl max-w-sm w-full">
            <div class="flex justify-between items-center relative">
                <h2 class="text-xl font-semibold">Add new Item</h2>
                <button class="text-red-600 border-1 border-gray-200 bg-white w-12 absolute -top-9 -right-9  h-12 rounded-full hover:bg-red-500 close-modal" data-modal-toggle="exampleModal">X</button>
            </div>
            <form action="{{ route('items.store', $supplier->id) }}" method="POST" enctype="multipart/form-data" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Item</label>
                    <input type="text" id="name" name="name" 
                    placeholder="Item name"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                    <input type="number" id="price" name="price" 
                    placeholder="Price"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea type="text" id="description" name="description" 
                    placeholder="Item Description"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required></textarea>
                </div>
                
                <div class="mb-4">
                    <div>
                        <label for="imageUpload" class="block text-sm font-medium text-gray-700">Image</label>
                        <div class="mt-1">
                            <input type="file" name="image" id="imageUpload"  accept="image/png, image/gif, image/jpeg" class="appearance-none px-3 py-2 w-full border border-gray-300 rounded shadow-sm focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500" required>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 close-modal" data-modal-toggle="exampleModal">Close</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    @section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        $(function () {
             $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

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

            $('#modalBtn').click(() => {
                $('#itemModal').removeClass('hidden');
                $('#itemModal').addClass('flex');

            });

            $('.close-modal').click(() => {
                $('#itemModal').removeClass('flex');
                $('#itemModal').addClass('hidden');
            });

            $(document).on('click', '.delete-item', function() {
                let itemId = $(this).attr('data-item');  
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/item-delete/" + itemId,  
                            type: 'DELETE',
                            success: function(response) {              
                                Swal.fire({
                                    title: "Deleted!",
                                    text: "Item successfully deleted!",
                                    icon: "success"
                                })
                                .then(() => {
                                    location.reload();
                                })
                            },
                            error: function(xhr, status, error) {
                                console.error(error);
                                alert('An error occurred while deleting the item.');
                            }
                        });
                    }
                });
            });

            var table = $('#item').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    type: "post",
                    url: "{{ route('items.list', $supplier->id) }}",
                },
                columns: [
                    {data: 'image', name: 'image'},
                    {data: 'name', name: 'name'},
                    {data: 'description', name: 'description'},
                    {data: 'price', name: 'price'},
                    {data: 'action', name: 'action'},
                ]
            });
            });
    </script>
    @endsection
</x-app-layout>