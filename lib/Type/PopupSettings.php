<?php

namespace RexGraphQL\Type\Popup;

class PopupSettings
{
    protected bool $showOnChange;
    protected bool $timeRestricted;
    protected ?\DateTime $startDate;
    protected ?\DateTime $endDate;
    protected string $articleLimitation;
    protected array $articleList;
    protected string $visibility;
    protected bool $showReopenButton;

    public function showOnChange(): bool
    {
        return $this->showOnChange;
    }

    public function isTimeRestricted(): bool
    {
        return $this->timeRestricted;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function getArticleLimitation(): string
    {
        return $this->articleLimitation;
    }

    public function getArticleList(): array
    {
        return $this->articleList;
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }

    public function showReopenButton(): bool
    {
        return $this->showReopenButton;
    }

    public static function getFromSettings(array $settings): self
    {
        $_settings = new self();
        $_settings->showOnChange = $settings['show_on_change'] != null;
        $_settings->timeRestricted = $settings['time'] === 'timer';
        $_settings->startDate = $_settings->isTimeRestricted() ? new \DateTime($settings['start_date']) : null;
        $_settings->endDate = $_settings->isTimeRestricted() ? new \DateTime($settings['end_date']) : null;
        $_settings->articleLimitation = $settings['article_limitation'];
        $_settings->articleList = explode(',', $settings['article_list']);
        $_settings->visibility = $settings['visibility'];
        $_settings->showReopenButton = $settings['show_reopen_button'] != null;
        return $_settings;
    }

}
