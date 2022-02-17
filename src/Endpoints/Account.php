<?php
namespace Budgetlens\CopernicaRestApi\Endpoints;

use Budgetlens\CopernicaRestApi\Resources\Account\Consumption;
use Budgetlens\CopernicaRestApi\Resources\Account\Identity;

class Account extends BaseEndpoint
{
    /**
     * @return Identity
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function identity(): Identity
    {
        $response = $this->performApiCall(
            'GET',
            "identity"
        );

        return new Identity(collect($response));
    }

    /**
     * @param \DateTime $begin
     * @param \DateTime|null $end
     * @return Consumption
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function consumption(\DateTime $begin, \DateTime $end = null)
    {
        $parameters = collect([
            'begin' => $begin,
            'end' => $end ?? new \DateTime(),
        ])->reject(function ($value) {
            return empty($value);
        })->map(function ($value) {
            return ($value instanceof \DateTime)
                ? $value->format('Y-m-d')
                : $value;
        });

        $response = $this->performApiCall(
            'GET',
            "consumption" . $this->buildQueryString($parameters->all())
        );

        return new Consumption(collect($response));
    }
}
