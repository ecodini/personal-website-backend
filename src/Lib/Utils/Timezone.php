<?php namespace Holamanola45\Www\Lib\Utils;
      
use DateTime;
use DateTimeZone;

class Timezone {
    public static function changeTimezoneToArg($date_str) {
        $date = new DateTime($date_str);
        $date->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));

        return $date;
    }

    public static function formatForSql(DateTime $date): string {
        return $date->format("Y-m-d H:i:s P");
    }

    public static function getCurrentDateString(): string {
        return self::formatForSql(self::changeTimezoneToArg('now'));
    }
}