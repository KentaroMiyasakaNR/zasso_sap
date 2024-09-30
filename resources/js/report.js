// スクリプトの重複読み込みを防ぐためのフラグ
if (typeof window.reportScriptLoaded === 'undefined') {
    window.reportScriptLoaded = true;

    // DOM elements
    const preview = document.getElementById('preview');
    const imagePreview = document.getElementById('imagePreview');
    const uploadForm = document.getElementById('uploadForm');
    const resultDiv = document.getElementById('result');
    const photoInput = document.getElementById('photo');
    const selectPhotoBtn = document.getElementById('selectPhotoBtn');
    const analyzeBtn = document.getElementById('analyzeBtn');

    // New DOM elements
    const reportBtn = document.getElementById('reportBtn');
    const reportModal = document.getElementById('reportModal');
    const modalPreview = document.getElementById('modalPreview');
    const confirmReportBtn = document.getElementById('confirmReportBtn');
    const cancelReportBtn = document.getElementById('cancelReportBtn');

    // Debug logging function
    function debugLog(message) {
        console.log(`[DEBUG] ${message}`);
    }

    // Function to preview the selected image
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                imagePreview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Function to analyze the image
    function analyzeImage(event) {
        event.preventDefault();
        console.log('同定分析analyzeImage called');
        
        if (!photoInput.files || photoInput.files.length === 0) {
            resultDiv.innerHTML = '<p>写真を選択してください。</p>';
            return;
        }
        
        const formData = new FormData(uploadForm);
        
        fetch(uploadForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.json();
        })
        .then(data => {
            console.log('サーバーからの応答:', data);
            if (data && data.content) {
                resultDiv.innerHTML = `<pre>${data.content}</pre>`;
                reportBtn.style.display = 'inline-block';
            } else {
                console.error('予期しないデータ構造:', data);
                resultDiv.innerHTML = '<p>データの取得に失敗しました。もう一度お試しください。</p>';
            }
        })
        .catch(error => {
            console.error('エラー:', error);
            resultDiv.innerHTML = '<p>エラーが発生しました。もう一度お試しください。</p>';
        });
    }

    // New function to show report modal
    function showReportModal() {
        modalPreview.src = preview.src;
        reportModal.style.display = 'block';
    }

    // New function to hide report modal
    function hideReportModal() {
        reportModal.style.display = 'none';
    }

    // Function to submit report
    function submitReport() {
        const formData = new FormData();
        formData.append('photo', photoInput.files[0]);
        formData.append('identification_result', resultDiv.innerText);
        
        // 位置情報を取得（オプション）
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(function(position) {
                formData.append('latitude', position.coords.latitude);
                formData.append('longitude', position.coords.longitude);
                sendReportData(formData);
            }, function(error) {
                console.error("位置情報の取得に失敗しました:", error);
                sendReportData(formData);
            });
        } else {
            sendReportData(formData);
        }
    }

    // Function to send report data
    function sendReportData(formData) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(reportStoreUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('報告が送信されました:', data);
            alert('報告が正常に送信されました。');
            hideReportModal();
        })
        .catch(error => {
            console.error('報告の送信中にエラーが発生しました:', error);
            alert('報告の送信中にエラーが発生しました。もう一度お試しください。');
        });
    }

    // Event listeners
    function initializeEventListeners() {
        debugLog('Initializing event listeners');
        
        selectPhotoBtn.addEventListener('click', function() {
            debugLog('selectPhotoBtn clicked');
            photoInput.click();
        });

        photoInput.addEventListener('change', function() {
            debugLog('File selected: ' + (this.files[0] ? this.files[0].name : 'No file'));
            previewImage(this);
        });
        
        // フォームの送信イベントを処理
        uploadForm.addEventListener('submit', analyzeImage);
        
        // New event listeners
        reportBtn.addEventListener('click', showReportModal);
        cancelReportBtn.addEventListener('click', hideReportModal);
        confirmReportBtn.addEventListener('click', submitReport);
        
        debugLog('Event listeners initialized');
    }

    // Check if elements are correctly loaded
    function checkElements() {
        debugLog('Checking elements');
        debugLog('selectPhotoBtn: ' + (selectPhotoBtn ? 'Found' : 'Not found'));
        debugLog('photoInput: ' + (photoInput ? 'Found' : 'Not found'));
        debugLog('uploadForm: ' + (uploadForm ? 'Found' : 'Not found'));
        debugLog('analyzeBtn: ' + (analyzeBtn ? 'Found' : 'Not found'));
    }

    // DOMContentLoaded イベントリスナーを一度だけ追加
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', onDOMContentLoaded);
    } else {
        onDOMContentLoaded();
    }

    function onDOMContentLoaded() {
        debugLog('DOMContentLoaded event fired');
        checkElements();
        initializeEventListeners();
    }

    // Expose previewImage to global scope if needed
    window.previewImage = previewImage;

    debugLog('Script loaded');
} else {
    console.warn('Report script already loaded. Skipping re-initialization.');
}