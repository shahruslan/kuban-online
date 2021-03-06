<?php


namespace KubanOnline;


use KubanOnline\Exceptions\CurlException;
use KubanOnline\Exceptions\JsonDecodeException;

class Api
{
    private function query(string $path, array $fields = []): array
    {
        $url = 'https://kuban-online.ru/api' . $path;

        $headers = [
            'x-requested-with: XMLHttpRequest',
        ];

        $ch = curl_init($url);


        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (env('DEBUG', false)) {
            $stream = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_STDERR, $stream);
        }

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $errno = curl_errno($ch);


        if (is_resource($ch)) {
            curl_close($ch);
        }

        if (0 !== $errno) {
            throw new CurlException($error, $errno);
        }


        $result = json_decode($response, true);
        $errno = json_last_error();
        $error = json_last_error_msg();


        if (env('DEBUG', false)) {
            rewind($stream);
            echo stream_get_contents($stream) . "\n";
            echo "Request data: " . print_r($fields, 1);
            echo "Response: " . var_export($response, 1) . "\n";
        }

        if ($errno != JSON_ERROR_NONE) {
            throw new JsonDecodeException($response, $error, $errno);
        }

        return $result;
    }

    public function getSpecialistTimes(string $doctorId, string $clinicId, string $patientId): array
    {
        $data = [
            'doctor_form-doctor_id' => $doctorId,
            'doctor_form-clinic_id' => $clinicId,
            'doctor_form-patient_id' => $patientId,
            'doctor_form-history_id' => '',
            'doctor_form-appointment_type' => '',
        ];

        return $this->query('/appointment_list/', $data);
    }

    public function getSpecialityList(string $clinicId, string $patientId): array
    {
        $data = [
            'clinic_form-clinic_id' => $clinicId,
            'clinic_form-patient_id' => $patientId,
            'clinic_form-history_id' => '',
            'clinic_form-patientAriaNumber' => '',
        ];

        return $this->query('/speciality_list/', $data);
    }

    public function getDoctorList(string $specialityId,  string $clinicId, string $patientId): array
    {
        $data = [
            'speciality_form-speciality_id' => $specialityId,
            'speciality_form-clinic_id' => $clinicId,
            'speciality_form-patient_id' => $patientId,
            'speciality_form-history_id' => '',
        ];

        return $this->query('/doctor_list/', $data);
    }
}