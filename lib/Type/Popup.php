<?php
/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author v.pallaoro@kreatif.it
 * Date: 08.30.21
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RexGraphQL\Type\Popup;


use Exception;
use RexGraphQL\Type\Structure\ArticleSlice;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;


#[Type]
class Popup
{
    const SETTINGS_ID = 20;
    protected string $sliceId;
    protected PopupUserInformation $data;
    protected PopupSettings $settings;
    protected bool $isVisible;
    protected bool $hasContentChanged;

    public function __construct(\rex_article_slice $slice, PopupUserInformation $data)
    {
        $this->sliceId = $slice->getId();
        $this->settings = PopupSettings::getFromSettings($slice->getValueArray(static::SETTINGS_ID));
        $this->data = $data;

        $this->setLastMod($slice);
        $this->setIsVisible();
    }

    #[Field]
    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    #[Field]
    public function showReopenButton(): bool
    {
        return $this->settings->showReopenButton();
    }

    #[Field]
    public function newData(): PopupUserInformation
    {
        return $this->data;
    }

    /**
     * @throws Exception
     */
    #[Field]
    public function getSlice(): ArticleSlice
    {
        return ArticleSlice::getById($this->sliceId);
    }

    private function setIsVisible(): void
    {
        $this->isVisible = $this->checkGeneralVisibility();

        if ($this->isVisible && $this->settings->isTimeRestricted()) {
            $this->isVisible = $this->checkTimeRestriction();
        }

        if ($this->isVisible && $this->settings->getArticleLimitation() !== 'everywhere') {
            $this->isVisible = $this->checkListedPages();
        }
    }

    private function checkGeneralVisibility(): bool
    {
        if ($this->settings->showOnChange() && $this->hasContentChanged) {
            $this->data->shownOnce = true;
            return true;
        } else {
            switch ($this->settings->getVisibility()) {
                case 'always':
                    return true;
                case 'close':
                    return !$this->data->closed;
                case 'once':
                    if($this->data->shownOnce) {
                        return false;
                    }
                    $this->data->shownOnce = true;
                    return true;
                default:
                    return false;
            }
        }
    }

    private function checkTimeRestriction(): bool
    {
        $now = new \DateTime();

        return $this->settings->getStartDate() >= $now && $now <= $this->settings->getEndDate();
    }

    private function checkListedPages(): bool
    {
        if (!$this->data->getCurrentArticleId()) {
            return false;
        }
        return match ($this->settings->getArticleLimitation()) {
            'blacklist' => !in_array($this->data->getCurrentArticleId(), $this->settings->getArticleList()),
            'whitelist' => in_array($this->data->getCurrentArticleId(), $this->settings->getArticleList()),
            default => false,
        };
    }

    private function setLastMod(\rex_article_slice $slice): void
    {
        $lastMod = $slice->getValue('updatedate');
        if (!$this->data->lastModified || $this->data->lastModified !== $lastMod) {
            $this->hasContentChanged = true;
            $this->data->lastModified = $lastMod;
        } else {
            $this->hasContentChanged = false;
        }
    }
}
