<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mobile Update QR
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 text-gray-900">
                <div class="bg-white py-8 px-6 flex flex-col justify-center items-center shadow rounded-lg mb-0 space-y-5">

                    <div id="qrcode"></div>
                    <span class="text-lg">Please Scan the QR to update</span>
                </div>
            </div>
        </div>
    </div>


    @section('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>  
        <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>

        <script>            
            $(document).ready(() => {
               
                new QRCode(document.getElementById("qrcode"), {
                    text: "{{ $ip }}/mobile-update",
                    width: 340,
                    height: 340,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.L
                });

            });
        </script>
    @endsection
</x-app-layout>