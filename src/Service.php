<?php


namespace KubanOnline;



use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class Service
{
    private Api $api;
    private FilesystemAdapter $cache;

    public function __construct()
    {
        $this->api = new Api();
        $this->cache = new FilesystemAdapter('', 120, 'cache');
    }

    public function check(string $doctorId)
    {
        if ($this->checkDate() == false) {
            return;
        }


        try {
            $result = $this->api->getSpecialistTimes($doctorId, $_ENV['CLINIC_ID'], $_ENV['PATIENT_ID']);
            $status = $result['success'] ? Status::makeOpen() : Status::makeClose();
        } catch (\RuntimeException $exception) {
            $status = Status::makeError();
        }

        $prevStatus = $this->getPrevStatus($status, $doctorId);

        if ($prevStatus == null || $prevStatus->status() != $status->status()) {
            $this->notify($status->changeStatusText($prevStatus));
        }
    }

    private function checkDate(): bool
    {
        $days = [
            5,
            6,
            7,
        ];

        $hoursFrom = 2;
        $hoursTo = 20;


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

    private function notify(string $text)
    {
        echo $text . "\n";
    }
}