<?php

namespace App\Markdown\CustomBlocks;

/**
 * Checklist Block for verification checklists
 *
 * Usage:
 * :::checklist
 * **Security Hardening** (REQUIRED)
 *
 * - Two-factor authentication enabled
 * - Admin URL is customized
 * - Security patches are up to date
 *
 * **Payment Configuration** (REQUIRED)
 *
 * - Hosted checkout mode enabled
 * - Test transactions complete successfully
 * :::
 */
class ChecklistBlock extends AbstractCustomBlock
{
    //
}
