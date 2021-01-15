<?php
namespace App\Http\Model\Traits;

trait Timestamp
{
    public function getCreatedAtAttribute($raw_time)
    {
        return $this->changeTimeZone($raw_time, '', config('app.timezone'));
    }

    public function getUpdatedAtAttribute($raw_time)
    {
        return $this->changeTimeZone($raw_time, '', config('app.timezone'));
    }

    public function changeTimeZone($dateString, $timeZoneSource = null, $timeZoneTarget = null)
    {

        if (empty($timeZoneSource)) {
            $timeZoneSource = date_default_timezone_get();
        }
        if (empty($timeZoneTarget)) {
            $timeZoneTarget = date_default_timezone_get();
        }

        $dt = new \DateTime($dateString, new \DateTimeZone($timeZoneSource));
        $dt->setTimezone(new \DateTimeZone($timeZoneTarget));
        return $dt->format('Y-m-d H:i:s');
    }
}