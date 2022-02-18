<?php
namespace Budgetlens\CopernicaRestApi\Enum;

enum FieldType: string
{
    case INTEGER = 'integer';
    case FLOAT = 'float';
    case DATE = 'date';
    case EMPTY_DATE = 'empty_date';
    case DATETIME = 'datetime';
    case EMPTY_DATETIME = 'empty_datetime';
    case TEXT = 'text';
    case EMAIL = 'email';
    case PHONE = 'phone';
    case PHONE_FAX = 'phone_fax';
    case PHONE_GSM = 'phone_gsm';
    case SELECT = 'select';
    case BIG = 'big';
    case FOREIGN_KEY = 'foreign_key';

    public function prepValue($value)
    {
        return match ($this) {
            FieldType::SELECT => is_array($value) ? implode("\n", $value) : $value,
            default => $value,
        };
    }
}
