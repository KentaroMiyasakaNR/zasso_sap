<x-app-layout>
    <x-slot name="header">
        <h2 style="text-align: center; font-size: 24px; color: #333;">
            {{ __('gairAi') }}
        </h2>
    </x-slot>

    <div style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
        <div>
            <h2 style="text-align: center; font-size: 20px; color: #333; margin-bottom: 30px;">ようこそ、{{ Auth::user()->name }}さん</h2>
            
            <div style="display: flex; flex-direction: column; align-items: center;">
                <a href="{{ route('report.create') }}" style="display: inline-block; padding: 12px 24px; background-color: #4CAF50; color: white; text-decoration: none; margin-bottom: 20px; text-align: center; width: 150px; border-radius: 25px; font-weight: bold; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    新規報告
                </a>
                <a href="{{ route('report.list') }}" style="display: inline-block; padding: 12px 24px; background-color: #2196F3; color: white; text-decoration: none; text-align: center; width: 150px; border-radius: 25px; font-weight: bold; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    報告一覧
                </a>
                @if(Auth::user()->role === 'SystemAdmin')
                <a href="{{ route('users.index') }}" style="display: inline-block; padding: 12px 24px; background-color: #FFA500; color: white; text-decoration: none; margin-top: 20px; text-align: center; width: 150px; border-radius: 25px; font-weight: bold; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    ユーザー一覧
                </a>
                @endif
                @if(auth()->user()->role === 'SystemAdmin')
                <a href="{{ route('report.map') }}" style="display: inline-block; padding: 12px 24px; background-color: #009688; color: white; text-decoration: none; margin-top: 20px; text-align: center; width: 150px; border-radius: 25px; font-weight: bold; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    報告マップ
                </a>
                @endif
                <button id="howToUseBtn" style="display: inline-block; padding: 12px 24px; background-color: #FFA500; color: white; text-decoration: none; margin-top: 20px; text-align: center; width: 150px; border-radius: 25px; font-weight: bold; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; cursor: pointer;">
                    使い方
                </button>
            </div>
        </div>
    </div>
    
    <!-- 使い方モーダル -->
    <div id="howToUseModal" style="display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
        <div style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: 10px;">
            <h2 style="text-align: center; margin-bottom: 20px;">使い方</h2>
            <img src="{{ asset('images/reports/sapTurorial.jpg') }}" alt="使い方" style="max-width: 100%; height: auto; border-radius: 8px; margin-bottom: 20px;">
            <div style="text-align: center;">
                <button class="closeModalBtn" style="padding: 10px 20px; background-color: #f44336; color: white; border: none; border-radius: 5px; cursor: pointer;">閉じる</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const howToUseBtn = document.getElementById('howToUseBtn');
            const howToUseModal = document.getElementById('howToUseModal');
            const closeModalBtn = document.querySelector('.closeModalBtn');

            function showModal(modal) {
                modal.style.display = 'block';
            }

            function hideModal(modal) {
                modal.style.display = 'none';
            }

            howToUseBtn.addEventListener('click', () => showModal(howToUseModal));

            closeModalBtn.addEventListener('click', () => hideModal(howToUseModal));

            window.addEventListener('click', function(event) {
                if (event.target == howToUseModal) {
                    hideModal(howToUseModal);
                }
            });
        });
    </script>
</x-app-layout>