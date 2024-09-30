let map;
let currentMarker;
let reports = []; // reportsを初期化

async function initMap() {
    const { Map } = await google.maps.importLibrary("maps");

    map = new Map(document.getElementById("map"), {
        center: { lat: 35.6812, lng: 139.7671 }, // 東京の座標
        zoom: 8,
    });

    await loadReports(); // 報告をロード
}

async function loadReports() {
    const response = await fetch(reportsApiUrl); // APIからデータを取得
    reports = await response.json(); // reportsにデータを格納

    const reportList = document.getElementById('report-list');
    reports.forEach(report => {
        const listItem = document.createElement('li');
        listItem.className = 'border-b pb-2 cursor-pointer hover:bg-gray-100';
        listItem.dataset.lat = report.latitude ?? 0;
        listItem.dataset.lng = report.longitude ?? 0;
        listItem.dataset.name = report.user.name;
        listItem.dataset.id = report.id; // IDをデータ属性に追加
        listItem.innerHTML = `<p class="text-sm text-gray-600">${new Date(report.created_at).toLocaleString()}</p>`;
        reportList.appendChild(listItem);
    });
}

async function showReportLocation(lat, lng, reporterName, reportId) {
    const { Marker } = await google.maps.importLibrary("marker");

    const position = { lat: parseFloat(lat), lng: parseFloat(lng) };
    map.setCenter(position);
    map.setZoom(15);

    if (currentMarker) {
        currentMarker.setMap(null);
    }

    currentMarker = new Marker({
        map: map,
        position: position,
        title: reporterName
    });

    displayReportDetail(reportId); // 報告詳細を表示
}

function displayReportDetail(reportId) {
    const report = reports.find(r => r.id == reportId); // IDで報告を検索
    const reportDetailContent = document.getElementById('report-detail-content');
    const reportDetail = document.getElementById('report-detail');
    if (report) {
        // 画像のURLを生成（storageUrlを使用）
        const imageUrl = `${storageUrl}/${report.photo_path}`;

        reportDetailContent.innerHTML = `
            <p><strong>日時:</strong> ${new Date(report.created_at).toLocaleString()}</p>
            <p><strong>画像:</strong> <img src="${imageUrl}" alt="報告画像" class="w-full h-auto" /></p>
            <p><strong>結果:</strong> ${report.identification_result}</p>
        `;
        reportDetail.classList.remove('hidden'); // 詳細を表示
    } else {
        console.error(`Report with ID ${reportId} not found`); // デバッグ用のエラーメッセージ
    }
}

function plotAllReports() {
    const reportList = document.getElementById('report-list').children;
    Array.from(reportList).forEach(report => {
        const position = { lat: parseFloat(report.dataset.lat), lng: parseFloat(report.dataset.lng) };
        const marker = new google.maps.Marker({
            position: position,
            map: map,
            title: report.dataset.name
        });

        // マーカーにクリックイベントを追加
        marker.addListener('click', () => {
            showReportLocation(report.dataset.lat, report.dataset.lng, report.dataset.name, report.dataset.id);
        });
    });
}

function setupEventListeners() {
    const reportList = document.getElementById('report-list');
    reportList.addEventListener('click', (event) => {
        const listItem = event.target.closest('li');
        if (listItem) {
            const lat = listItem.dataset.lat;
            const lng = listItem.dataset.lng;
            const name = listItem.dataset.name;
            const id = listItem.dataset.id; // IDを取得
            showReportLocation(lat, lng, name, id); // IDを渡す
        }
    });

    const plotAllButton = document.getElementById('plot-all-reports');
    plotAllButton.addEventListener('click', plotAllReports);
}

document.addEventListener('DOMContentLoaded', () => {
    initMap();
    setupEventListeners(); // DOMが読み込まれた後にイベントリスナーを設定
});
