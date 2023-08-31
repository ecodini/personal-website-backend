<?php namespace Holamanola45\Www\Lib\Utils;
      
use DateTime;
use DateTimeZone;

class Timezone {
    public static function newCurrentTimestampArg($date_str) {
        $date = new DateTime($date_str);
        $date->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));

        return $date;
    }

    public static function newCurrentTimestampUTC($date_str) {
        $date = new DateTime($date_str);
        return $date;
    }

    public static function formatForSql(DateTime $date): string {
        return $date->format("Y-m-d H:i:s");
    }

    public static function getCurrentDateString(): string {
        return self::formatForSql(self::newCurrentTimestampUTC('now'));
    }
}