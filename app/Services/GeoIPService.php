<?php

namespace App\Services;

use GeoIp2\Database\Reader;

class GeoIPService
{
    protected $reader;

    public function __construct()
    {
        $this->reader = new Reader(storage_path('app/GeoLite2-City.mmdb'));
    }

    public function isBlocked($ip)
    {
        $blockedRegions = [
            'alabama', 'virginia', 'utah', 'arkansas', 'louisiana', 'montana',
            'texas', 'mississippi', 'california',
            'europe', 'india', 'china', 'south korea', 'indonesia',
            'united arab emirates', 'egypt', 'turkey', 'iran'
        ];

        try {
            $record = $this->reader->city($ip);
            $region = strtolower($record->subdivisions[0]->name ?? '');
            $country = strtolower($record->country->name ?? '');
            $continent = strtolower($record->continent->code ?? '');

            foreach ($blockedRegions as $blocked) {
                if (
                    str_contains($region, $blocked) ||
                    str_contains($country, $blocked) ||
                    ($blocked === 'europe' && $continent === 'eu')
                ) {
                    return true;
                }
            }
        } catch (\Exception $e) {
            // Si no se puede determinar la IP, no bloquear
        }

        return false;
    }
}
