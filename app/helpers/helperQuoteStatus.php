<?php
use App\Enums\QuotationStatus;

if (! function_exists('quote_status_badge')) {
    /**
     * @param  QuotationStatus|string|null  $quote_status
     * @return string  HTML Badge
     */
    function quote_status_badge(null|string|QuotationStatus $quote_status): string
    {
        // แปลง string → enum
        if (is_string($quote_status)) {
            $quote_status = QuotationStatus::from($quote_status);
        } elseif (!$quote_status instanceof QuotationStatus) {
            $quote_status = QuotationStatus::Wait;
        }

        return sprintf(
            '<span class="badge %s">%s</span>',
            $quote_status->badgeClass(),
            $quote_status->label()
        );
    }
}
