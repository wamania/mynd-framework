<?php

class MfDate
{
    private $datetime;

    public static function now()
    {
        $datetime = new Datetime();
        return new MfDate($datetime);
    }

    public static function fromDatetime($datetime)
    {
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s',  $datetime);
        return new MfDate($datetime);
    }

    public static function fromDate($date)
    {
        $datetime = DateTime::createFromFormat('Y-m-d', $date);
        return new MfDate($datetime);
    }

    public static function fromFormat($format, $date)
    {
        $datetime = DateTime::createFromFormat($format, $date);
        return new MfDate($datetime);
    }

    public static function fromTimestamp($timestamp)
    {
        $datetime = new Datetime('@'.$timestamp);
        return new MfDate($datetime);
        //$datetime->setTimezone(new DateTimeZone(date_default_timezone_get()));
    }

    public function __construct($datetime = null)
    {
        if (is_null($datetime)) {
            $this->datetime = new Datetime();
        } else {
            $this->datetime = $datetime;
        }
    }

    public function format($format)
    {
        return $this->datetime->format($format);
    }

    public function to_iso8601()
    {
        return $this->datetime->format(DateTime::ISO8601);
    }

    public function to_timestamp()
    {
        return $this->datetime->format('U');
    }

    public function to_date()
    {
        return $this->datetime->format('Y-m-d');
    }

    public function to_datetime()
    {
        return $this->datetime->format('Y-m-d H:i:s');
    }

    /**
     * Use date() instead of Datetime::format() because date() use locales
     * @return string
     */
    public function to_shortText($onlyDate = false)
    {
        if ($onlyDate) {
            return strftime('%a %e %b %Y', $this->to_timestamp());
        }
        return strftime('%a %e %b %Y %R', $this->to_timestamp());
    }

    /**
     * Use date() instead of Datetime::format() because date() use locales
     * @return string
     */
    public function to_longText($onlyDate = false)
    {
        if ($onlyDate) {
            return strftime('%A %e %B %Y', $this->to_timestamp());
        }
        return strftime('%A %e %B %Y %R', $this->to_timestamp());
    }

    public function to_betterLocal($onlyDate = false)
    {
        if ($onlyDate) {
            return strftime('%x', $this->to_timestamp());
        }
        return strftime('%x %X', $this->to_timestamp());
    }
}