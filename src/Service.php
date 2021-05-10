<?php


namespace KubanOnline;



use KubanOnline\Dto\Doctor;
use KubanOnline\Dto\Speciality;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class Service
{
    private Api $api;
    private FilesystemAdapter $cache;
    private string $clinicId;
    private string $patientId;

    public function __construct()
    {
        $this->api = new Api();
        $this->cache = new FilesystemAdapter('', env('CACHE_TIME', 86400), 'cache');
        $this->clinicId = (string)env('CLINIC_ID');
        $this->patientId = (string)env('PATIENT_ID');
    }

    public function check(string $doctorId, bool $force = false)
    {
        if ($force === true && $this->checkDate() == false) {
            return;
        }


        try {
            $result = $this->api->getSpecialistTimes($doctorId, $this->clinicId, $this->patientId);
            $status = $result['success'] ? Status::makeOpen() : Status::makeClose();
        } catch (\RuntimeException $exception) {
            $status = Status::makeError();
        }

        $prevStatus = $this->getPrevStatus($status, $doctorId);

        if ($force === true) {
            $this->notify($status->statusText(), $doctorId);
        } elseif ($prevStatus == null || $prevStatus->status() != $status->status()) {
            $this->notify($status->statusText($prevStatus), $doctorId);
        }
    }

    /**
     * @return array|Speciality[]
     */
    public function specialities(): array
    {
        $result = [];

        $data = $this->api->getSpecialityList($this->clinicId, $this->patientId);
        $this->checkError($data);

        foreach ($data['response'] as $item) {
            $result[] = new Speciality([
                'id' => $item['IdSpesiality'],
                'name' => $item['NameSpesiality'],
                'tickets' => (int)$item['CountFreeTicket'],
            ]);
        }

        return $result;
    }

    public function doctors(string $specialityId): array
    {
        $result = [];

        $data = $this->api->getDoctorList($specialityId, $this->clinicId, $this->patientId);
        $this->checkError($data);

        foreach ($data['response'] as $item) {
            $result[] = new Doctor([
                'id' => $item['IdDoc'],
                'name' => $item['Name'],
                'tickets' => (int)$item['CountFreeTicket'],
            ]);
        }

        return $result;
    }

    private function checkDate(): bool
    {
        $days = config('days', [5, 6, 7]);
        $hoursFrom = config('time.from', 16);
        $hoursTo = config('time.to', 20);

        $currentDay = (int)date('N');
        $currentHour = (int)date('G');

        if (in_array($currentDay, $days) == false) {
            return false;
        }

        if ($currentHour < $hoursFrom || $currentHour >= $hoursTo) {
            return false;
        }

        return true;
    }

    private function getPrevStatus(Status $status, string $doctorId): ?Status
    {
        $key = "times-{$doctorId}";

        $cacheItem = $this->cache->getItem($key);

        if ($cacheItem->isHit()) {
            /** @var Status $prevStatus */
            $prevStatus = $cacheItem->get();
            return $prevStatus;
        }

        $cacheItem->set($status);
        $this->cache->save($cacheItem);

        return null;
    }

    private function notify(string $text, $doctorId)
    {
        $doctors = config('doctors', []);

        if (array_key_exists($doctorId, $doctors)) {
            $text = "Доктор {$doctors[$doctorId]} - $text";
        }

        new Telegram(env('TELEGRAM_BOT_API'), env('TELEGRAM_BOT_NAME'));

        Request::sendMessage([
            'chat_id' => env('TELEGRAM_CHAT_ID'),
            'text' => $text,
        ]);
    }

    private function checkError(array $response)
    {
        if ($response['success'] !== false) {
            return;
        }

        if (array_key_exists('ErrorDescription', $response['error'])) {
            throw new \RuntimeException($response['error']['ErrorDescription'], $response['error']['IdError']);
        }

        $message = array_pop($response['error']);
        throw new \RuntimeException($message);
    }
}