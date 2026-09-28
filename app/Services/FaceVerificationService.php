<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SapientPro\ImageComparatorLaravel\Facades\Comparator;
use SapientPro\ImageComparator\Strategy\DifferenceHashStrategy;

class FaceVerificationService
{
    /**
     * Get the microservice URL from config or env
     */
    public static function getServiceUrl(): string
    {
        return config('services.face_recognition.url', env('FACE_SERVICE_URL', 'http://127.0.0.1:5005'));
    }

    /**
     * Get default tolerance (0.50 = strict matching, lower is stricter)
     */
    public static function getDefaultTolerance(): float
    {
        return (float) config('services.face_recognition.tolerance', env('FACE_SERVICE_TOLERANCE', 0.50));
    }

    /**
     * Get request timeout in seconds
     */
    public static function getTimeout(): int
    {
        return (int) config('services.face_recognition.timeout', env('FACE_SERVICE_TIMEOUT', 4));
    }

    /**
     * Get Python binary path
     */
    public static function getPythonBinary(): ?string
    {
        $customPath = config('services.face_recognition.python_bin', env('FACE_SERVICE_PYTHON_BIN'));
        if ($customPath && file_exists($customPath)) {
            return $customPath;
        }

        $venvPython = base_path('services/face_service/venv/bin/python3');
        if (file_exists($venvPython)) {
            return $venvPython;
        }

        $systemPython = '/usr/bin/python3';
        if (file_exists($systemPython)) {
            return $systemPython;
        }

        return null;
    }

    /**
     * Get Python face_verifier script path
     */
    public static function getScriptPath(): ?string
    {
        $scriptPath = base_path('services/face_service/face_verifier.py');
        return file_exists($scriptPath) ? $scriptPath : null;
    }

    /**
     * Verify two face images.
     *
     * @param string $referenceImagePath Path to registered profile photo
     * @param string $capturedImagePath Path to newly clicked selfie
     * @param float|null $tolerance Custom tolerance (lower = stricter)
     * @return array ['status' => bool, 'match' => bool, 'similarity' => float, 'distance' => ?float, 'engine' => string, 'message' => string, 'error_code' => ?string]
     */
    public static function verify(string $referenceImagePath, string $capturedImagePath, ?float $tolerance = null): array
    {
        $tolerance = $tolerance ?? self::getDefaultTolerance();

        //  dd($referenceImagePath);
        if (!file_exists($referenceImagePath)) {
            return [
                'status' => false,
                'match' => false,
                'similarity' => 0,
                'distance' => null,
                'engine' => 'none',
                'error_code' => 'REF_FILE_NOT_FOUND',
                'message' => 'Registered profile image not found on server.',
            ];
        }

        // dd($capturedImagePath);
        if (!file_exists($capturedImagePath)) {
            return [
                'status' => false,
                'match' => false,
                'similarity' => 0,
                'distance' => null,
                'engine' => 'none',
                'error_code' => 'QUERY_FILE_NOT_FOUND',
                'message' => 'Selfie image file not found on server.',
            ];
        }

        // 1. Try Python HTTP Microservice (Fastest & In-memory)
        try {
            $serviceUrl = self::getServiceUrl();
            $timeout = self::getTimeout();

            $response = Http::timeout($timeout)->post(rtrim($serviceUrl, '/') . '/verify', [
                'reference_image' => $referenceImagePath,
                'query_image' => $capturedImagePath,
                'tolerance' => $tolerance,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (is_array($data) && !empty($data['status']) && isset($data['match'])) {
                    return [
                        'status' => true,
                        'match' => (bool)$data['match'],
                        'similarity' => (float)($data['similarity'] ?? 0),
                        'distance' => isset($data['distance']) ? (float)$data['distance'] : null,
                        'engine' => 'python_service',
                        'message' => $data['message'] ?? ($data['match'] ? 'Face matched successfully' : 'Face mismatch detected'),
                        'error_code' => $data['error_code'] ?? null,
                    ];
                } elseif (is_array($data) && in_array($data['error_code'] ?? '', ['NO_FACE_IN_REF', 'NO_FACE_IN_QUERY'])) {
                    return [
                        'status' => false,
                        'match' => false,
                        'similarity' => 0,
                        'distance' => null,
                        'engine' => 'python_service',
                        'message' => $data['message'] ?? 'Face not detected in image',
                        'error_code' => $data['error_code'],
                    ];
                }
            }
        } catch (Exception $e) {
            Log::debug('Face service HTTP failed, falling back to CLI: ' . $e->getMessage());
        }

        // 2. Try Python CLI in Virtualenv
        $pythonBin = self::getPythonBinary();
        $scriptPath = self::getScriptPath();

        if ($pythonBin && $scriptPath) {
            $cmd = escapeshellcmd($pythonBin) . ' ' .
                   escapeshellarg($scriptPath) . ' --compare ' .
                   escapeshellarg($referenceImagePath) . ' ' .
                   escapeshellarg($capturedImagePath) . ' --tolerance ' .
                   escapeshellarg((string)$tolerance) . ' 2>&1';

            $output = @shell_exec($cmd);
            if (!empty($output)) {
                $data = json_decode(trim($output), true);
                if (is_array($data) && !empty($data['status']) && isset($data['match'])) {
                    return [
                        'status' => true,
                        'match' => (bool)$data['match'],
                        'similarity' => (float)($data['similarity'] ?? 0),
                        'distance' => isset($data['distance']) ? (float)$data['distance'] : null,
                        'engine' => 'python_cli',
                        'message' => $data['message'] ?? ($data['match'] ? 'Face matched successfully' : 'Face mismatch detected'),
                        'error_code' => $data['error_code'] ?? null,
                    ];
                } elseif (is_array($data) && in_array($data['error_code'] ?? '', ['NO_FACE_IN_REF', 'NO_FACE_IN_QUERY'])) {
                    return [
                        'status' => false,
                        'match' => false,
                        'similarity' => 0,
                        'distance' => null,
                        'engine' => 'python_cli',
                        'message' => $data['message'] ?? 'Face not detected in image',
                        'error_code' => $data['error_code'],
                    ];
                }
            }
        }

        // 3. Fallback to PHP Image Comparator if Python is unavailable
        try {
            if (class_exists(Comparator::class)) {
                Comparator::setHashStrategy(new DifferenceHashStrategy());
                $similarity = Comparator::compare($referenceImagePath, $capturedImagePath);
                $isMatch = ($similarity >= 30);

                return [
                    'status' => true,
                    'match' => $isMatch,
                    'similarity' => round((float)$similarity, 2),
                    'distance' => null,
                    'engine' => 'php_comparator_fallback',
                    'error_code' => null,
                    'message' => $isMatch
                        ? 'Face matched successfully (Fallback Engine).'
                        : 'Face mismatch detected! Real face match nahi hua.',
                ];
            }
        } catch (Exception $e) {
            Log::error('PHP Comparator fallback error: ' . $e->getMessage());
        }

        return [
            'status' => false,
            'match' => false,
            'similarity' => 0,
            'distance' => null,
            'engine' => 'error',
            'error_code' => 'ALL_ENGINES_FAILED',
            'message' => 'Face verification engine unavailable.',
        ];
    }

    /**
     * Verify a captured face image from base64 data against a reference image file.
     *
     * @param string $referenceImagePath Path to registered profile photo
     * @param string $base64Image Base64 encoded selfie image data
     * @param float|null $tolerance Custom tolerance
     * @return array
     */
    public static function verifyBase64(string $referenceImagePath, string $base64Image, ?float $tolerance = null): array
    {
        $tempFile = self::saveBase64ToTempFile($base64Image);
        if (!$tempFile) {
            return [
                'status' => false,
                'match' => false,
                'similarity' => 0,
                'distance' => null,
                'engine' => 'none',
                'error_code' => 'INVALID_BASE64_IMAGE',
                'message' => 'Failed to process base64 image data.',
            ];
        }

        try {
            $result = self::verify($referenceImagePath, $tempFile, $tolerance);
        } finally {
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }

        return $result;
    }

    /**
     * Detect face in an image.
     *
     * @param string $imagePath Path to image file
     * @return array ['status' => bool, 'has_face' => bool, 'face_count' => int, 'message' => string]
     */
    public static function detectFace(string $imagePath): array
    {
        if (!file_exists($imagePath)) {
            return [
                'status' => false,
                'has_face' => false,
                'face_count' => 0,
                'message' => 'Image file not found on server.',
            ];
        }

        // 1. Try Python HTTP Microservice
        try {
            $serviceUrl = self::getServiceUrl();
            $timeout = self::getTimeout();

            $response = Http::timeout($timeout)->post(rtrim($serviceUrl, '/') . '/detect', [
                'image_path' => $imagePath,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (is_array($data) && isset($data['has_face'])) {
                    return [
                        'status' => $data['status'] ?? true,
                        'has_face' => (bool)$data['has_face'],
                        'face_count' => (int)($data['face_count'] ?? ($data['has_face'] ? 1 : 0)),
                        'locations' => $data['locations'] ?? [],
                        'engine' => 'python_service',
                        'message' => $data['message'] ?? ($data['has_face'] ? 'Face detected' : 'No face detected'),
                    ];
                }
            }
        } catch (Exception $e) {
            Log::debug('Face detect HTTP failed, falling back to CLI: ' . $e->getMessage());
        }

        // 2. Try Python CLI
        $pythonBin = self::getPythonBinary();
        $scriptPath = self::getScriptPath();

        if ($pythonBin && $scriptPath) {
            $cmd = escapeshellcmd($pythonBin) . ' ' .
                   escapeshellarg($scriptPath) . ' --detect ' .
                   escapeshellarg($imagePath) . ' 2>&1';

            $output = @shell_exec($cmd);
            if (!empty($output)) {
                $data = json_decode(trim($output), true);
                if (is_array($data) && isset($data['has_face'])) {
                    return [
                        'status' => $data['status'] ?? true,
                        'has_face' => (bool)$data['has_face'],
                        'face_count' => (int)($data['face_count'] ?? ($data['has_face'] ? 1 : 0)),
                        'locations' => $data['locations'] ?? [],
                        'engine' => 'python_cli',
                        'message' => $data['message'] ?? ($data['has_face'] ? 'Face detected' : 'No face detected'),
                    ];
                }
            }
        }

        return [
            'status' => false,
            'has_face' => false,
            'face_count' => 0,
            'engine' => 'none',
            'message' => 'Face detection engine unavailable.',
        ];
    }

    /**
     * Detect face in a base64 encoded image string.
     *
     * @param string $base64Image Base64 image data
     * @return array
     */
    public static function detectFaceBase64(string $base64Image): array
    {
        $tempFile = self::saveBase64ToTempFile($base64Image);
        if (!$tempFile) {
            return [
                'status' => false,
                'has_face' => false,
                'face_count' => 0,
                'message' => 'Failed to process base64 image data.',
            ];
        }

        try {
            $result = self::detectFace($tempFile);
        } finally {
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }

        return $result;
    }

    /**
     * Health check for face verification service and engines.
     *
     * @return array
     */
    public static function healthCheck(): array
    {
        $httpOk = false;
        $httpMessage = 'Service not running';
        $serviceUrl = self::getServiceUrl();

        try {
            $response = Http::timeout(2)->get(rtrim($serviceUrl, '/') . '/health');
            if ($response->successful()) {
                $httpOk = true;
                $httpMessage = 'HTTP microservice is running';
            }
        } catch (Exception $e) {
            $httpMessage = $e->getMessage();
        }

        $pythonBin = self::getPythonBinary();
        $scriptPath = self::getScriptPath();
        $cliOk = ($pythonBin !== null && $scriptPath !== null && file_exists($pythonBin) && file_exists($scriptPath));

        return [
            'http_service' => [
                'available' => $httpOk,
                'url' => $serviceUrl,
                'status' => $httpMessage,
            ],
            'cli_engine' => [
                'available' => $cliOk,
                'python_bin' => $pythonBin,
                'script_path' => $scriptPath,
            ],
            'active_engine' => $httpOk ? 'python_service' : ($cliOk ? 'python_cli' : 'php_comparator_fallback'),
            'ready' => ($httpOk || $cliOk),
        ];
    }

    /**
     * Check if any Face Verification Engine is available.
     *
     * @return bool
     */
    public static function isAvailable(): bool
    {
        $health = self::healthCheck();
        return $health['ready'] ?? false;
    }

    /**
     * Save base64 string to a temporary file.
     *
     * @param string $base64Image
     * @return string|null Path to temp file or null on failure
     */
    protected static function saveBase64ToTempFile(string $base64Image): ?string
    {
        try {
            // Remove data URI scheme prefix if present (e.g. data:image/jpeg;base64,)
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            }

            $decoded = base64_decode($base64Image);
            if ($decoded === false) {
                return null;
            }

            $tempPath = sys_get_temp_dir() . '/face_tmp_' . Str::random(16) . '.jpg';
            if (file_put_contents($tempPath, $decoded) === false) {
                return null;
            }

            @chmod($tempPath, 0666);
            return $tempPath;
        } catch (Exception $e) {
            Log::error('Failed to save base64 to temp file: ' . $e->getMessage());
            return null;
        }
    }
}
