<?php

namespace RexGraphQL\Type\Popup;

use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Input;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

#[Input]
#[Type]
class PopupUserInformation
{
    #[Field]
    public ?bool $closed = false;
    #[Field]
    public ?bool $shownOnce = false;
    #[Field]
    private ?ID $currentArticleId;

    #[Field]
    public ?int $lastModified = 0;


    public function getCurrentArticleId(): int
    {
        return $this->currentArticleId?->val() || \rex_article::getCurrentId();
    }

    public function setCurrentArticleId(?ID $id): void
    {
        $this->currentArticleId = $id;
    }
}
