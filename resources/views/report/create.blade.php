<x-app-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 24px; color: #333;">
                {{ __('gairAI') }}
            </h2>
            <div style="display: flex; align-items: center;">
                <a href="{{ asset('PDF/gairAI利用規約.pdf') }}" target="_blank" style="color: #2196F3; text-decoration: underline; margin-right: 20px; font-size: 14px;">
                    gairAI利用規約
                </a>
                <div id="pointDisplay" style="font-size: 18px; font-weight: bold; color: #4CAF50;">
                    0P
                </div>
            </div>
        </div>
    </x-slot>
    <div style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
        <div>
            <h2 style="text-align: center; font-size: 20px; color: #333; margin-bottom: 30px;">植物の写真をアップロード</h2>
            <div style="display: flex; flex-direction: column; align-items: center;">
                <form id="uploadForm" method="POST" enctype="multipart/form-data" style="width: 100%; max-width: 300px;" action="{{ route('report.analyze') }}">
                    @csrf  <!-- この行を追加 -->
                    <div style="margin-bottom: 20px;">
                        <input type="file" name="photo" id="photo" accept="image/*" style="display: none;">
                        <button type="button" id="selectPhotoBtn" style="display: inline-block; width: 100%; padding: 12px 24px; background-color: #4CAF50; color: white; text-decoration: none; text-align: center; border-radius: 25px; font-weight: bold; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; cursor: pointer; margin-bottom: 10px;">
                            ①写真を選択
                        </button>
                    </div>
                   
                    <div id="imagePreview" style="margin-top: 20px; display: none;">
                        <img id="preview" src="#" alt="プレビュー" style="max-width: 100%; height: auto; border-radius: 8px;">
                    </div>
                   
                    <div style="margin-top: 20px;">
                        <button type="submit" id="analyzeBtn" style="display: inline-block; width: 100%; padding: 12px 24px; background-color: #2196F3; color: white; text-decoration: none; text-align: center; border-radius: 25px; font-weight: bold; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; cursor: pointer;">
                            ②植物を同定
                        </button>
                    </div>
                </form>
                <div id="result" style="margin-top: 20px; text-align: center;"></div>
                <button id="reportBtn" style="display: none; margin-top: 20px; padding: 12px 24px; background-color: #FF5722; color: white; text-decoration: none; text-align: center; border-radius: 25px; font-weight: bold; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; cursor: pointer;">
                    ③報告（データ登録は現在はされません）
                </button>
            </div>
        </div>
    </div>

    <!-- モーダル -->
    <div id="reportModal" style="display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
        <div style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px; border-radius: 10px;">
            <h2 style="text-align: center; margin-bottom: 20px;">この内容を報告しますか？</h2>
            <img id="modalPreview" src="#" alt="選択した写真" style="max-width: 100%; height: auto; border-radius: 8px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-around;">
                <button id="confirmReportBtn" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;">報告する</button>
                <button id="cancelReportBtn" style="padding: 10px 20px; background-color: #f44336; color: white; border: none; border-radius: 5px; cursor: pointer;">キャンセル</button>
            </div>
        </div>
    </div>

    <!-- 音声ファイル -->
    <audio id="getPointsSound" src="{{ asset('sounds/getpoints.mp3') }}"></audio>

    <!-- 報告一覧イメージボタン -->
    <div style="text-align: center; margin-top: 20px;">
        <button id="showReportListBtn" style="padding: 12px 24px; background-color: #3498db; color: white; text-decoration: none; text-align: center; border-radius: 25px; font-weight: bold; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; cursor: pointer;">
            みんなの報告一覧 地図のイメージ
        </button>
    </div>

    <!-- 報告一覧イメージモーダル -->
    <div id="reportListModal" style="display: none; position: fixed; z-index: 2; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
        <div style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: 10px;">
            <h2 style="text-align: center; margin-bottom: 20px;">報告された植物は、地図上で場所を見ることができる想定です</h2>
            <img src="{{ asset('images/reports/みんなの報告をマップ.jpg') }}" alt="報告一覧イメージ" style="max-width: 100%; height: auto; border-radius: 8px; margin-bottom: 20px;">
            <div style="text-align: center;">
                <button id="closeReportListBtn" style="padding: 10px 20px; background-color: #f44336; color: white; border: none; border-radius: 5px; cursor: pointer;">閉じる</button>
            </div>
        </div>
    </div>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/report.js'])

    <script>
        var reportStoreUrl = "{{ route('report.store') }}";
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const showReportListBtn = document.getElementById('showReportListBtn');
            const reportListModal = document.getElementById('reportListModal');
            const closeReportListBtn = document.getElementById('closeReportListBtn');
            const reportBtn = document.getElementById('reportBtn');
            const reportModal = document.getElementById('reportModal');
            const backBtn = document.getElementById('backBtn');
            const modalPreview = document.getElementById('modalPreview');
            const pointDisplay = document.getElementById('pointDisplay');
            const imagePreview = document.getElementById('imagePreview');
            const result = document.getElementById('result');
            const preview = document.getElementById('preview');
            const modalPointDisplay = document.getElementById('modalPointDisplay');
            const getPointsSound = document.getElementById('getPointsSound');

            let points = 0;

            showReportListBtn.addEventListener('click', function() {
                reportListModal.style.display = 'block';
            });

            closeReportListBtn.addEventListener('click', function() {
                reportListModal.style.display = 'none';
            });

            reportBtn.addEventListener('click', function() {
                points += 10;
                pointDisplay.textContent = points + 'P';
                modalPointDisplay.textContent = '現在のポイント: ' + points + 'P';

                reportModal.style.display = 'block';
                modalPreview.src = "{{ asset('images/reports/10pointsGet.jpg') }}";
                modalPreview.style.opacity = '0';
                let opacity = 0;
                const fadeIn = setInterval(() => {
                    opacity += 0.1;
                    modalPreview.style.opacity = opacity;
                    if (opacity >= 1) {
                        clearInterval(fadeIn);
                        getPointsSound.play();
                    }
                }, 50);
            });

            backBtn.addEventListener('click', function() {
                reportModal.style.display = 'none';
                imagePreview.style.display = 'none';
                result.innerHTML = '';
                reportBtn.style.display = 'none';
                preview.src = '#';
            });

            window.addEventListener('click', function(event) {
                if (event.target == reportListModal) {
                    reportListModal.style.display = 'none';
                }
                if (event.target == reportModal) {
                    reportModal.style.display = 'none';
                }
            });
        });
    </script>
</x-app-layout>