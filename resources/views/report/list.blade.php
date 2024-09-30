<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('報告一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($reports as $report)
                        <div class="border rounded-lg p-4 relative bg-gray-50">
                            <div class="mb-2">
                                <strong>報告者：</strong> {{ $report->user->name }}
                            </div>
                            <div class="mb-2">
                                <strong>報告日時：</strong> {{ $report->created_at->format('Y年m月d日 H:i') }}
                            </div>
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $report->photo_path) }}" alt="報告写真" class="w-full h-48 object-cover rounded">
                            </div>
                            <div class="mb-4">
                                <strong>報告内容：</strong> {{ $report->identification_result }}
                            </div>
                            <div class="mt-4 flex space-x-2">
                                <a href="{{ route('report.edit', $report->id) }}" class="inline-block px-4 py-2 bg-blue-500 text-white font-bold rounded hover:bg-blue-600 transition duration-300 ease-in-out no-underline !important">
                                    編集
                                </a>
                                <form action="{{ route('report.destroy', $report->id) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-block px-4 py-2 bg-red-500 text-white font-bold rounded hover:bg-red-600 transition duration-300 ease-in-out cursor-pointer !important">
                                        削除
                                    </button>
                                </form>
                                @if($report->latitude && $report->longitude)
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $report->latitude }},{{ $report->longitude }}" target="_blank" class="inline-block px-4 py-2 bg-green-500 text-white font-bold rounded hover:bg-green-600 transition duration-300 ease-in-out no-underline !important">
                                        位置情報
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 p-4">
                    {{ $reports->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    /* スタイルの上書きを確実にするためのCSS */
    .inline-block.px-4.py-2.bg-blue-500,
    .inline-block.px-4.py-2.bg-red-500,
    .inline-block.px-4.py-2.bg-green-500 {
        display: inline-block !important;
        padding: 0.5rem 1rem !important;
        color: white !important;
        font-weight: bold !important;
        border-radius: 0.25rem !important;
        text-decoration: none !important;
        transition: background-color 0.3s ease-in-out !important;
    }
    .inline-block.px-4.py-2.bg-blue-500 {
        background-color: #3b82f6 !important;
    }
    .inline-block.px-4.py-2.bg-blue-500:hover {
        background-color: #2563eb !important;
    }
    .inline-block.px-4.py-2.bg-red-500 {
        background-color: #ef4444 !important;
    }
    .inline-block.px-4.py-2.bg-red-500:hover {
        background-color: #dc2626 !important;
    }
    .inline-block.px-4.py-2.bg-green-500 {
        background-color: #10b981 !important;
    }
    .inline-block.px-4.py-2.bg-green-500:hover {
        background-color: #059669 !important;
    }
</style>