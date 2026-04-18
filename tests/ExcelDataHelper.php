<?php
/**
 * ExcelDataHelper.php
 * Đọc test_data.json (export từ Excel bởi excel_to_json.py)
 * Ghi kết quả vào phpunit_results.json (để write_results.py đẩy ngược vào Excel)
 */
class ExcelDataHelper
{
    private static string $dataPath    = __DIR__ . '/../../test-data/w_login_data.json';
    private static string $resultsPath = __DIR__ . '/../../test-data/w_login_results.json';

    /** Load toàn bộ JSON */
    public static function load(): array
    {
        if (!file_exists(self::$dataPath)) {
            throw new \RuntimeException(
                "Không tìm thấy test_data.json. Chạy: python test-data/excel_to_json.py"
            );
        }
        return json_decode(file_get_contents(self::$dataPath), true);
    }

    /**
     * Trả về danh sách test case cho @dataProvider
     * Format: [ tc_id => [tc_id, email, password, expected], ... ]
     */
    public static function provider(): array
    {
        $json = self::load();
        $rows = [];
        foreach ($json['testcases'] as $tcId => $d) {
            $rows[$tcId] = [$tcId, $d['method'], $d['email'], $d['password'], $d['expected']];
        }
        return $rows;
    }

    /**
     * Trả về danh sách seed accounts từ JSON
     * Format: [ email => ['password' => ..., 'role' => ...], ... ]
     */
    public static function seeds(): array
    {
        try {
            $json = self::load();
            return $json['seeds'] ?? [];
        } catch (\RuntimeException $e) {
            return [];
        }
    }

    /** Ghi 1 kết quả vào phpunit_results.json */
    public static function writeResult(string $tcId, string $actual, string $status): void
    {
        $results = [];
        if (file_exists(self::$resultsPath)) {
            $results = json_decode(file_get_contents(self::$resultsPath), true) ?? [];
        }
        $results[$tcId] = ['actual' => $actual, 'status' => $status];
        file_put_contents(
            self::$resultsPath,
            json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}
