<?php
/**
 *  @author: $rachow 
 */

namespace TicketTailor\Webhook\Traits;

trait EnumsToArray
{
    /**
     * Grab the names of the enums.
     *
     * @return array
     *
     */
    public static function getNames(): array
    {
        return array_column(self::cases(), 'names');
    }

    /**
     * Grab the values of enums.
     *
     * @return array
     *
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Grab the array of enums.
     *
     * @return array
     *
     */
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
