<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('報告マップ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="flex">
                        <!-- Google Map -->
                        <div id="map" class="w-2/3 h-96"></div>

                        <!-- 報告一覧 -->
                        <div class="w-1/3 ml-4 overflow-y-auto h-96">
                            <h3 class="text-lg font-semibold mb-4">報告一覧</h3>
                            <ul id="report-list" class="space-y-2">
                                <!-- 報告一覧はJavaScriptで動的に生成 -->
                            </ul>
                            <button id="plot-all-reports" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded">すべての報告をプロット</button>
                        </div>
                    </div>
                    <!-- 報告詳細表示 -->
                    <div id="report-detail" class="mt-4 p-4 border border-gray-300 rounded hidden">
                        <h4 class="font-semibold">報告詳細</h4>
                        <div id="report-detail-content">
                            <!-- 報告詳細の内容がここに表示される -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&v=weekly"></script>
    <script>
        // 動的に生成されたベースURLを含めたAPIのURL
        var reportsApiUrl = "{{ url('api/reports') }}";
        // ストレージのベースURLを動的に設定
        var storageUrl = "{{ url('/') }}/storage";
    </script>
    @vite('resources/js/report-map.js') <!-- Viteを使用してJSを読み込む -->
</x-app-layout>
