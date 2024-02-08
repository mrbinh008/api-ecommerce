<?php

namespace App\Console\Commands;

use App\Models\District;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Console\Command;

class CrawlDataArea extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:geo-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl provinces, districts, and communes data and save to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Crawling provinces, districts, and communes data...');
        // Lấy dữ liệu tỉnh từ API
        $provincesResponse = $this->sendApiRequest('https://api.mysupership.vn/v1/partner/areas/province');

        $provincesData = $provincesResponse['results'];

        foreach ($provincesData as $provinceData) {
            // Lưu dữ liệu tỉnh vào bảng provinces
            $province = Province::updateOrCreate([
                'name' => $provinceData['name'],
            ]);

            // Lấy dữ liệu huyện từ API
            $districtsResponse = $this->sendApiRequest('https://api.mysupership.vn/v1/partner/areas/district', [
                'province' => $provinceData['code'],
            ]);

            $districtsData = $districtsResponse['results'];

            foreach ($districtsData as $districtData) {
                // Lưu dữ liệu huyện vào bảng districts
                $district = District::updateOrCreate( [
                    'name' => $districtData['name'],
                    'province_id' => $province->id,
                ]);

                // Lấy dữ liệu xã từ API
                $communesResponse = $this->sendApiRequest('https://api.mysupership.vn/v1/partner/areas/commune', [
                    'district' => $districtData['code'],
                ]);

                $communesData = $communesResponse['results'];

                foreach ($communesData as $communeData) {
                    // Lưu dữ liệu xã vào bảng communes
                    Ward::updateOrCreate([
                        'name' => $communeData['name'],
                        'district_id' => $district->id,
                    ]);
                }
            }
        }

        $this->info('Provinces, districts, and communes data crawled and saved to database.');
    }
    private function sendApiRequest($url, $params = [])
    {
        $ch = curl_init();

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        curl_close($ch);

        return json_decode($response, true);
    }
}
