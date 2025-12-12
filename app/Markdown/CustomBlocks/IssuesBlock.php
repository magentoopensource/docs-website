<?php

namespace App\Markdown\CustomBlocks;

/**
 * Issues Block for troubleshooting accordions
 *
 * Usage:
 * :::issues
 * ### Payment Gateway Returns "Invalid Credentials" | CRITICAL
 * Payment transactions fail with authentication errors.
 *
 * **Solution:**
 * - Verify API credentials
 * - Check live/test mode
 *
 * ### Checkout Throws 500 Error | WARNING
 * Intermittent errors during checkout.
 *
 * **Solution:**
 * - Check var/log/system.log
 * - Clear cache
 * :::
 */
class IssuesBlock extends AbstractCustomBlock
{
    //
}
