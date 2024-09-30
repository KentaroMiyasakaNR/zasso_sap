<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reports = Report::with('user')->get(); // 必要なデータを取得
        return view('report-map.index', compact('reports')); // Bladeファイルを返す
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('report.create');
    }

    /**
     * 新しい報告を保存する
     */
    public function store(Request $request)
    {
        $request->validate([
            'identification_result' => 'required|string',
            'photo' => 'required|image|max:10240', // 10MBまでの画像ファイル
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        // 画像を保存
        $photoPath = $request->file('photo')->store('reports', 'public');

        // 報告を作成
        $report = Report::create([
            'user_id' => Auth::id(),
            'identification_result' => $request->identification_result,
            'photo_path' => $photoPath,
            'reported_at' => now(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status_id' => 1, // デフォルトのステータスID
        ]);

        // 植物名を抽出して保存（簡易的な実装）
        $plantNames = explode(',', $request->plant_names);
        foreach ($plantNames as $name) {
            $plant = Plant::firstOrCreate(['name' => trim($name)]);
            $report->plants()->attach($plant->id);
        }

        return response()->json([
            'message' => '報告が正常に保存されました。',
            'report_id' => $report->id
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        return view('reports.edit', compact('report'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report)
    {
        $validatedData = $request->validate([
            'content' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $report->identification_result = $validatedData['content'];

        if ($request->hasFile('photo')) {
            if ($report->photo_path) {
                Storage::disk('public')->delete($report->photo_path);
            }
            $path = $request->file('photo')->store('photos', 'public');
            $report->photo_path = $path;
        }

        $report->save();

        return redirect()->route('report.list')->with('success', '報告が更新されました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        $report->delete();

        return redirect()->route('reports.index')->with('success', '報告が削除されました。');
    }

    // make PHP / CURL compliant with multidimensional arrays
function curl_setopt_custom_postfields($ch, $postfields, $headers = null) {
    $algos = hash_algos();
    $hashAlgo = null;
  
    foreach (array('sha1', 'md5') as $preferred) {
      if (in_array($preferred, $algos)) {
        $hashAlgo = $preferred;
        break;
      }
    }
  
    if ($hashAlgo === null) {
      list($hashAlgo) = $algos;
    }
  
    $boundary = '----------------------------' . substr(hash(
      $hashAlgo, 'cURL-php-multiple-value-same-key-support' . microtime()
    ), 0, 12);
  
    $body = array();
    $crlf = "\r\n";
    $fields = array();
  
    foreach ($postfields as $key => $value) {
      if (is_array($value)) {
        foreach ($value as $v) {
          $fields[] = array($key, $v);
        }
      } else {
        $fields[] = array($key, $value);
      }
    }
  
    foreach ($fields as $field) {
      list($key, $value) = $field;
  
      if (strpos($value, '@') === 0) {
        preg_match('/^@(.*?)$/', $value, $matches);
        list($dummy, $filename) = $matches;
  
        $body[] = '--' . $boundary;
        $body[] = 'Content-Disposition: form-data; name="' . $key . '"; filename="' . basename($filename) . '"';
        $body[] = 'Content-Type: application/octet-stream';
        $body[] = '';
        $body[] = file_get_contents($filename);
      } else {
        $body[] = '--' . $boundary;
        $body[] = 'Content-Disposition: form-data; name="' . $key . '"';
        $body[] = '';
        $body[] = $value;
      }
    }
  
    $body[] = '--' . $boundary . '--';
    $body[] = '';
  
    $contentType = 'multipart/form-data; boundary=' . $boundary;
    $content = join($crlf, $body);
  
    $contentLength = strlen($content);
  
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Length: ' . $contentLength,
      'Expect: 100-continue',
      'Content-Type: ' . $contentType
    ));
  
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
  }
  

    public function analyze(Request $request)
    {
        try {
            $request->validate([
                'photo' => 'required|image|max:10240', // 10MBまでの画像ファイル
            ]);

            $image = $request->file('photo');
            $imagePath = $image->store('temp', 'public');
            $fullPath = Storage::disk('public')->path($imagePath);

            $PROJECT = "all";
            $API_LANG = '&lang=ja'; // 日本語指定
            $url = 'https://my-api.plantnet.org/v2/identify/' . $PROJECT . '?api-key=' . config('services.plantnet.api_key') . $API_LANG;
            $data = [
                'organs' => ['auto'],
                'images' => ['@' . $fullPath]
            ];

            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
            ]);

            $this->curl_setopt_custom_postfields($ch, $data);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                Log::error('cURL Error: ' . curl_error($ch));
                throw new \Exception('PlantNet APIへの接続中にエラーが発生しました: ' . curl_error($ch));
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode != 200) {
                Log::error('PlantNet API Error: HTTP Code ' . $httpCode, ['response' => $response]);
                throw new \Exception('PlantNet APIからエラーレスポンスを受け取りました。HTTP Code: ' . $httpCode);
            }

            curl_close($ch);

            Storage::disk('public')->delete($imagePath);

            Log::info('Raw PlantNet API Response:', ['response' => $response]);

            $result = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('JSON解析エラー: ' . json_last_error_msg());
            }

            if (empty($result) || !isset($result['results'])) {
                Log::error('Invalid API response:', ['response' => $response]);
                throw new \Exception('PlantNet APIからの応答が無効です。詳細: ' . print_r($result, true));
            }

            $formattedResponse = $this->formatPlantNetResponseAsString($result);

            if (empty($formattedResponse)) {
                throw new \Exception('フォーマットされた応答が空です。');
            }

            return response()->json(['content' => $formattedResponse]);
        } catch (\Exception $e) {
            Log::error('Analyze Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function formatPlantNetResponseAsString($result)
    {
        $formattedString = "";
        $count = 0;

        if (isset($result['results']) && is_array($result['results'])) {
            foreach ($result['results'] as $item) {
                if ($count >= 10) break;

                $commonNames = $item['species']['commonNames'] ?? [];
                $japaneseCommonName = $this->findJapaneseCommonName($commonNames);

                if ($japaneseCommonName) {
                    $species = $japaneseCommonName;
                    $family = $item['species']['family']['scientificNameWithoutAuthor'] ?? '不明';

                    $formattedString .= "植物名：{$species}\n";
                    $formattedString .= "　　科：{$family}\n\n";

                    $count++;
                }
            }
        }

        return trim($formattedString);
    }

    private function findJapaneseCommonName($commonNames)
    {
        foreach ($commonNames as $name) {
            if ($this->isJapanese($name)) {
                return $name;
            }
        }
        return null;
    }

    private function isJapanese($string)
    {
        return preg_match('/[\p{Han}\p{Hiragana}\p{Katakana}]/u', $string);
    }

    public function list()
    {
        $reports = Report::with('user')->latest()->paginate(6);
        return view('report.list', compact('reports'));
    }

    public function getReports()
    {
        $reports = Report::with('user')->get(); // 必要なデータを取得
        return response()->json($reports); // JSON形式で返す
    }
}
