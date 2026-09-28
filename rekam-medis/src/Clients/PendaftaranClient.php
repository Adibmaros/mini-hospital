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

    private function request(string $method, string $path, ?array $body = null): array
    {
        $url = $this->baseUrl . $path;
        $ch = curl_init();

        $headers = [
            'Accept: application/json',
            'X-API-KEY: ' . $this->apiKey
        ];

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
        ];

        if ($body !== null) {
            $jsonBody = json_encode($body);
            $headers[] = 'Content-Type: application/json';
            $options[CURLOPT_POSTFIELDS] = $jsonBody;
        }

        $options[CURLOPT_HTTPHEADER] = $headers;
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            error_log("PendaftaranClient error: " . $err);
            return ['success' => false, 'code' => 503, 'message' => "Gagal terhubung ke Sistem Pendaftaran: {$err}"];
        }

        $decoded = json_decode($response ?: '', true);
        return [
            'success' => ($httpCode >= 200 && $httpCode < 300),
            'code' => $httpCode,
            'data' => $decoded['data'] ?? null,
            'message' => $decoded['message'] ?? "HTTP {$httpCode}"
        ];
    }

    public function getKunjungan(?string $tanggal = null, ?string $poli = null, ?string $status = null): array
    {
        $query = [];
        if ($tanggal) $query['tanggal'] = $tanggal;
        if ($poli) $query['poli'] = $poli;
        if ($status) $query['status'] = $status;

        $path = '/api/kunjungan' . (!empty($query) ? '?' . http_build_query($query) : '');
        return $this->request('GET', $path);
    }

    public function getKunjunganById(int $id): array
    {
        return $this->request('GET', "/api/kunjungan/{$id}");
    }

    public function getPasien(int $idPasien): array
    {
        return $this->request('GET', "/api/pasien/{$idPasien}");
    }

    public function updateKunjunganStatus(int $idKunjungan, string $status = 'selesai'): array
    {
        return $this->request('PATCH', "/api/kunjungan/{$idKunjungan}/status", ['status' => $status]);
    }
}
