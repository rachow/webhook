<?php
/**
 *  @author: $rachow 
 */

namespace TicketTailor\Webhook\Traits;

trait EnumsToArray
{
    public static function getNames(): array
    {
        return array_column(self::cases(), 'names');
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function toArray(): array
    {
        if (empty(self::getNames())) {
            return self::getValues();
        }

        if (empty(self::getValues())) {
            return self::getNames();
        }

        return array_column(self::cases(), 'value', 'name');
    }
}