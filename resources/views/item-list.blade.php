<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $supplier?->supplier }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="#" class="py-2 px-4 inline-block bg-green-500 text-white hover:bg-green-800 rounded-sm mb-6">
                Add Item
            </a>
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


    @section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.js"></script>
    <script type="text/javascript">
        $(function () {
             $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
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