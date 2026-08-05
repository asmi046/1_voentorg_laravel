<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = public_path('tmp/tableConvert.com_e527f5.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found: {$csvPath}");
            return;
        }

        $file = fopen($csvPath, 'r');
        $headers = fgetcsv($file, 0, ',');
        
        $regions = [];
        $districts = [];
        $districtMap = [];
        
        $this->command->info('Processing CSV data...');
        
        rewind($file);
        fgetcsv($file, 0, ',');
        
        $lineCount = 0;
        while (($line = fgets($file)) !== false) {
            $lineCount++;
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            
            $line = trim($line, '"');
            $row = explode(',', $line);
            
            $regionType = $row[1] ?? null;
            $regionName = $row[2] ?? null;
            $districtType = $row[3] ?? null;
            $districtName = $row[4] ?? null;
            
            if ($lineCount <= 3) {
                $this->command->info("Line {$lineCount}: region=" . var_export($regionName, true) . ", district=" . var_export($districtName, true));
            }
            
            $regionKey = $regionName;
            if (!isset($regions[$regionKey]) && $regionName) {
                $regions[$regionKey] = [
                    'type' => $regionType,
                    'name' => $regionName,
                    'federal_district' => $row[19] ?? null,
                ];
            }
            
            if ($districtName) {
                $districtKey = $districtName . '_' . $regionKey;
                if (!isset($districts[$districtKey])) {
                    $districts[$districtKey] = [
                        'type' => $districtType,
                        'name' => $districtName,
                        'region_name' => $regionName,
                    ];
                }
            }
        }
        
        fclose($file);
        
        $this->command->info('Creating regions...');
        $regionMap = [];
        foreach ($regions as $region) {
            $regionModel = \App\Models\Region::create($region);
            $regionMap[$region['name']] = $regionModel->id;
        }
        
        $this->command->info('Creating districts...');
        $districtIdMap = [];
        foreach ($districts as $district) {
            $regionId = $regionMap[$district['region_name']] ?? null;
            $districtModel = \App\Models\District::create([
                'type' => $district['type'],
                'name' => $district['name'],
                'region_id' => $regionId,
            ]);
            $districtIdMap[$district['name'] . '_' . $district['region_name']] = $districtModel->id;
        }
        
        $this->command->info('Importing cities...');
        $file = fopen($csvPath, 'r');
        fgetcsv($file, 0, ',');
        
        $count = 0;
        while (($line = fgets($file)) !== false) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            
            $line = trim($line, '"');
            $row = explode(',', $line);
            
            if (count($row) < 6) {
                continue;
            }
            
            $regionName = $row[2] ?? null;
            $districtName = $row[4] ?? null;
            
            $regionId = $regionMap[$regionName] ?? null;
            $districtId = null;
            
            if ($districtName && $regionName) {
                $districtId = $districtIdMap[$districtName . '_' . $regionName] ?? null;
            }
            
            $cityData = [
                'post_index' => $row[0] ?? null,
                'type' => $row[5] ?? null,
                'name' => $row[6] ?? null,
                'region_id' => $regionId,
                'district_id' => $districtId,
                'settlement_type' => $row[7] ?? null,
                'settlement' => $row[8] ?? null,
                'kladr_code' => $row[9] ?? null,
                'fias_code' => $row[10] ?? null,
                'fias_level' => $row[11] ?? null,
                'center_sign' => !empty($row[12]) ? (int)$row[12] : null,
                'okato_code' => $row[13] ?? null,
                'oktmo_code' => $row[14] ?? null,
                'tax_code' => $row[15] ?? null,
                'timezone' => $row[16] ?? null,
                'latitude' => !empty($row[17]) ? (float)$row[17] : null,
                'longitude' => !empty($row[18]) ? (float)$row[18] : null,
                'federal_district' => $row[19] ?? null,
                'population' => !empty($row[20]) ? (int)$row[20] : null,
            ];

            try {
                \App\Models\City::create($cityData);
                $count++;
                
                if ($count % 100 === 0) {
                    $this->command->info("Imported {$count} cities...");
                }
            } catch (\Exception $e) {
                $this->command->error("Error importing city: " . ($row[6] ?? 'unknown') . " - " . $e->getMessage());
                $this->command->error("Data: " . json_encode($cityData));
            }
        }
        
        fclose($file);
        
        $this->command->info("Successfully imported {$count} cities!");
        $this->command->info("Created " . count($regions) . " regions and " . count($districts) . " districts.");
    }
}
