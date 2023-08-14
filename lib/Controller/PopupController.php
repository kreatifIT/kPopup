<?php

namespace RexGraphQL\Controller\Popup;

use RexGraphQL\Service\PopupService;
use RexGraphQL\Type\Popup\Popup;
use RexGraphQL\Type\Popup\PopupUserInformation;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Query;

class PopupController
{
    private PopupService $service;
    public function __construct()
    {
        $this->service = new PopupService();
    }

    /**
     * @param PopupUserInformation[] $data
     * @return Popup[]
     */
    #[Query]
    #[Logged]
    public function getPopups(array $data): array
    {
        return $this->service->getPopups($data);
    }
}
