<?php
declare(strict_types=1);

namespace App\Clients;

use App\Core\Env;

class PendaftaranClient
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(Env::get('PENDAFTARAN_URL', 'http://pendaftaran'), '/');
        $this->apiKey = Env::get('API_KEY', 'ganti-dengan-kunci-rahasia');
    }

    public function getPasien(int $idPasien): ?array
    {
        $url = $this->baseUrl . '/api/pasien/' . $idPasien;
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'X-API-KEY: ' . $this->apiKey
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err || $httpCode !== 200) {
            error_log("PendaftaranClient getPasien failed for ID {$idPasien}: " . ($err ?: "HTTP {$httpCode}"));
            return [
                'id_pasien' => $idPasien,
                'no_rm' => 'RM-UNKNOWN',
                'nama' => "Pasien #{$idPasien}"
            ];
        }

        $data = json_decode($response ?: '', true);
        if (isset($data['status']) && $data['status'] === 'success' && isset($data['data'])) {
            return $data['data'];
        }

        return [
            'id_pasien' => $idPasien,
            'no_rm' => 'RM-UNKNOWN',
            'nama' => "Pasien #{$idPasien}"
        ];
    }
}
