<?php
namespace Budgetlens\CopernicaRestApi\Resources\Database;

use Budgetlens\CopernicaRestApi\Resources\BaseResource;

class Intentions extends BaseResource
{
    public $email;
    public $sms;
    public $fax;
    public $pdf;
}
