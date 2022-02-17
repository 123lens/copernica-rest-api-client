<?php
namespace Budgetlens\CopernicaRestApi\Endpoints;

use Budgetlens\CopernicaRestApi\Resources\Account\Identity;

class Account extends BaseEndpoint
{
    /**
     * @return Identity
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function identity()
    {
        $response = $this->performApiCall(
            'GET',
            "identity"
        );

        return new Identity(collect($response));
    }

}
