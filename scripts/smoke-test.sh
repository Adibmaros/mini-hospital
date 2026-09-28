#!/usr/bin/env bash
set -e

# Base URLs
PENDAFTARAN_URL="${PENDAFTARAN_URL:-http://localhost:8001}"
REKAM_MEDIS_URL="${REKAM_MEDIS_URL:-http://localhost:8002}"
FARMASI_URL="${FARMASI_URL:-http://localhost:8003}"
API_KEY="${API_KEY:-ganti-dengan-kunci-rahasia}"

echo "=================================================="
echo " Running Smoke Tests for Mini Hospital EAI System "
echo "=================================================="

# Helper function
check_status() {
    local name="$1"
    local expected="$2"
    local actual="$3"
    if [ "$actual" -eq "$expected" ]; then
        echo "[PASS] $name (HTTP $actual)"
    else
        echo "[FAIL] $name - Expected HTTP $expected, got HTTP $actual"
        exit 1
    fi
}

# 1. Health Checks
echo ""
echo "--- 1. Testing Health Endpoints ---"
CODE=$(curl -s -o /dev/null -w "%{http_code}" "$PENDAFTARAN_URL/api/health")
check_status "Pendaftaran Health Check" 200 "$CODE"

CODE=$(curl -s -o /dev/null -w "%{http_code}" "$REKAM_MEDIS_URL/api/health")
check_status "Rekam Medis Health Check" 200 "$CODE"

CODE=$(curl -s -o /dev/null -w "%{http_code}" "$FARMASI_URL/api/health")
check_status "Farmasi Health Check" 200 "$CODE"

# 2. Authentication Checks (E13)
echo ""
echo "--- 2. Testing API Authentication (E13) ---"
CODE=$(curl -s -o /dev/null -w "%{http_code}" "$PENDAFTARAN_URL/api/pasien/1")
check_status "Pendaftaran API without X-API-KEY should return 401" 401 "$CODE"

CODE=$(curl -s -o /dev/null -w "%{http_code}" -H "X-API-KEY: wrong_key" "$PENDAFTARAN_URL/api/pasien/1")
check_status "Pendaftaran API with wrong X-API-KEY should return 401" 401 "$CODE"

# 3. Pendaftaran API Checks
echo ""
echo "--- 3. Testing Pendaftaran API ---"
CODE=$(curl -s -o /dev/null -w "%{http_code}" -H "X-API-KEY: $API_KEY" "$PENDAFTARAN_URL/api/pasien/1")
check_status "GET /api/pasien/1" 200 "$CODE"

CODE=$(curl -s -o /dev/null -w "%{http_code}" -H "X-API-KEY: $API_KEY" "$PENDAFTARAN_URL/api/kunjungan?tanggal=$(date +%Y-%m-%d)")
check_status "GET /api/kunjungan" 200 "$CODE"

# 4. Farmasi Master Obat Checks (A5)
echo ""
echo "--- 4. Testing Farmasi Master Obat (A5) ---"
CODE=$(curl -s -o /dev/null -w "%{http_code}" -H "X-API-KEY: $API_KEY" "$FARMASI_URL/api/obat")
check_status "GET /api/obat" 200 "$CODE"

# 5. POST Resep to Farmasi (E4)
echo ""
echo "--- 5. Testing POST /api/resep to Farmasi ---"
UNIQUE_ID=$(( (RANDOM % 90000) + 10000 ))
RESEP_PAYLOAD=$(cat <<EOF
{
  "id_rm": 999,
  "id_pasien": 1,
  "items": [
    {
      "id_resep": $UNIQUE_ID,
      "nama_obat": "Paracetamol 500 mg",
      "dosis": "500 mg",
      "jumlah": 2,
      "aturan_pakai": "3x1 sesudah makan"
    }
  ]
}
EOF
)

CODE=$(curl -s -o /dev/null -w "%{http_code}" -X POST -H "X-API-KEY: $API_KEY" -H "Content-Type: application/json" -d "$RESEP_PAYLOAD" "$FARMASI_URL/api/resep")
check_status "POST /api/resep" 201 "$CODE"

# 6. Idempotency Check (E11 / E9)
echo ""
echo "--- 6. Testing Idempotency on Resep ---"
CODE=$(curl -s -o /dev/null -w "%{http_code}" -X POST -H "X-API-KEY: $API_KEY" -H "Content-Type: application/json" -d "$RESEP_PAYLOAD" "$FARMASI_URL/api/resep")
check_status "Duplicate POST /api/resep should return 409 Conflict" 409 "$CODE"

# 7. Invalid Medicine Name Check (E14 / A5)
echo ""
echo "--- 7. Testing Invalid Medicine Name Validation (E14) ---"
INVALID_PAYLOAD=$(cat <<EOF
{
  "id_rm": 999,
  "id_pasien": 1,
  "items": [
    {
      "id_resep": $((UNIQUE_ID + 1)),
      "nama_obat": "Obat-Fiktif-Tidak-Ada-Di-Master-123",
      "dosis": "100 mg",
      "jumlah": 1,
      "aturan_pakai": "1x1"
    }
  ]
}
EOF
)
CODE=$(curl -s -o /dev/null -w "%{http_code}" -X POST -H "X-API-KEY: $API_KEY" -H "Content-Type: application/json" -d "$INVALID_PAYLOAD" "$FARMASI_URL/api/resep")
check_status "POST /api/resep with invalid medicine name should return 422" 422 "$CODE"

# 8. PATCH Status Resep (E6)
echo ""
echo "--- 8. Testing PATCH /api/resep/{id}/status ---"
CODE=$(curl -s -o /dev/null -w "%{http_code}" -X PATCH -H "X-API-KEY: $API_KEY" -H "Content-Type: application/json" -d '{"status":"selesai"}' "$FARMASI_URL/api/resep/$UNIQUE_ID/status")
check_status "PATCH /api/resep/$UNIQUE_ID/status = selesai" 200 "$CODE"

# 9. PATCH Status Kunjungan Pendaftaran (A6)
echo ""
echo "--- 9. Testing PATCH /api/kunjungan/{id}/status ---"
CODE=$(curl -s -o /dev/null -w "%{http_code}" -X PATCH -H "X-API-KEY: $API_KEY" -H "Content-Type: application/json" -d '{"status":"selesai"}' "$PENDAFTARAN_URL/api/kunjungan/1/status")
check_status "PATCH /api/kunjungan/1/status = selesai" 200 "$CODE"

echo ""
echo "=================================================="
echo " All Smoke Tests Passed Successfully! (Exit Code 0)"
echo "=================================================="
exit 0
