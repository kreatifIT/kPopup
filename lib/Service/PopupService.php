<?php

namespace RexGraphQL\Service;


use Kreatif\Project\Settings;
use RexGraphQL\Type\Popup\Popup;
use RexGraphQL\Type\Popup\PopupUserInformation;

class PopupService
{
    /**
     * @param PopupUserInformation[] $data
     * @return Popup[]
     */
    public function getPopups(array $data): array
    {
        $slices = \rex_article_slice::getSlicesForArticle(Settings::getArticleId('popup_page_id'), \rex_clang::getCurrentId());
        $i = -1;
        return array_map(function ($slice) use ($data, &$i) {
            $i++;
            return new Popup($slice, $data[$i] ?? $data[0]);
        }, $slices);
    }
}
