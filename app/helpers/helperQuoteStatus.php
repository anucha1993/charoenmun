<?php
use App\Enums\QuotationStatus;

if (! function_exists('quote_status_badge')) {
    /**
     * @param  QuotationStatus|string|null  $status
     * @return string  HTML Badge
     */
    function quote_status_badge(null|string|QuotationStatus $status): string
    {
        // แปลง string → enum
        if (is_string($status)) {
            $status = QuotationStatus::from($status);
        } elseif (!$status instanceof QuotationStatus) {
            $status = QuotationStatus::Wait;
        }

        return sprintf(
            '<span class="badge %s">%s</span>',
            $status->badgeClass(),
            $status->label()
        );
    }
}
