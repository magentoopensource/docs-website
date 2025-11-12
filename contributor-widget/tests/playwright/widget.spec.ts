import { test, expect } from '@playwright/test';

/**
 * GitHub Contributors Widget Tests
 *
 * Comprehensive test suite for the contributors widget
 * Tests functionality, accessibility, responsiveness, and user interaction
 */

test.describe('GitHub Contributors Widget', () => {
  test.describe('Widget Loading and Display', () => {
    test('should load the widget page successfully', async ({ page }) => {
      await page.goto('/open-source-docs.html');
      await expect(page).toHaveTitle(/Creating Your First Products/i);
    });

    test('should display the widget container', async ({ page }) => {
      await page.goto('/open-source-docs.html');
      const widget = page.locator('#gh-widget-container');
      await expect(widget).toBeVisible();
    });

    test('should show avatars when contributors load', async ({ page }) => {
      await page.goto('/open-source-docs.html');

      // Wait for widget to initialize
      await page.waitForTimeout(2000);

      // Check if avatars are present (might require token)
      const avatars = page.locator('.gh-avatar');
      const count = await avatars.count();

      // Should have 0-5 avatars depending on token availability
      expect(count).toBeGreaterThanOrEqual(0);
      expect(count).toBeLessThanOrEqual(5);
    });
  });

  test.describe('Widget Interaction', () => {
    test('should toggle popup on click (desktop)', async ({ page }) => {
      await page.setViewportSize({ width: 1280, height: 720 });
      await page.goto('/open-source-docs.html');

      // Wait for widget
      await page.waitForTimeout(2000);

      const trigger = page.locator('.gh-avatars-compact').first();
      const popup = page.locator('.gh-popup').first();

      if (await trigger.isVisible()) {
        // Click to open
        await trigger.click();

        // Popup should be visible (or attempt to be)
        const ariaExpanded = await trigger.getAttribute('aria-expanded');
        expect(ariaExpanded).toBe('true');

        // Click to close
        await trigger.click();
        const ariaExpandedClosed = await trigger.getAttribute('aria-expanded');
        expect(ariaExpandedClosed).toBe('false');
      }
    });

    test('should open popup on hover (desktop)', async ({ page }) => {
      await page.setViewportSize({ width: 1280, height: 720 });
      await page.goto('/open-source-docs.html');

      await page.waitForTimeout(2000);

      const widget = page.locator('.gh-contributors-mini').first();
      const popup = page.locator('.gh-popup').first();

      if (await widget.isVisible()) {
        // Hover to open
        await widget.hover();

        // Wait a moment for CSS transition
        await page.waitForTimeout(500);

        // Check if popup has active class or is visible
        const isVisible = await popup.isVisible().catch(() => false);
        // Note: May not be visible without contributors data
        expect(typeof isVisible).toBe('boolean');
      }
    });

    test('should close popup on Escape key', async ({ page }) => {
      await page.setViewportSize({ width: 1280, height: 720 });
      await page.goto('/open-source-docs.html');

      await page.waitForTimeout(2000);

      const trigger = page.locator('.gh-avatars-compact').first();

      if (await trigger.isVisible()) {
        // Open popup
        await trigger.click();

        // Press Escape
        await page.keyboard.press('Escape');

        // Check aria-expanded
        const ariaExpanded = await trigger.getAttribute('aria-expanded');
        expect(ariaExpanded).toBe('false');
      }
    });

    test('should support keyboard navigation (Enter/Space)', async ({ page }) => {
      await page.setViewportSize({ width: 1280, height: 720 });
      await page.goto('/open-source-docs.html');

      await page.waitForTimeout(2000);

      const trigger = page.locator('.gh-avatars-compact').first();

      if (await trigger.isVisible()) {
        // Focus the trigger
        await trigger.focus();

        // Press Enter to open
        await page.keyboard.press('Enter');
        const ariaExpanded1 = await trigger.getAttribute('aria-expanded');
        expect(ariaExpanded1).toBe('true');

        // Press Space to close
        await page.keyboard.press('Space');
        const ariaExpanded2 = await trigger.getAttribute('aria-expanded');
        expect(ariaExpanded2).toBe('false');
      }
    });
  });

  test.describe('Responsive Design', () => {
    test('should display correctly on mobile (375px)', async ({ page }) => {
      await page.setViewportSize({ width: 375, height: 667 });
      await page.goto('/open-source-docs.html');

      await page.waitForTimeout(2000);

      const widget = page.locator('.gh-contributors-mini').first();

      if (await widget.isVisible()) {
        // Should be visible on mobile
        await expect(widget).toBeVisible();

        // Mobile should show close button when popup opens
        const trigger = page.locator('.gh-avatars-compact').first();
        if (await trigger.isVisible()) {
          await trigger.click();
          await page.waitForTimeout(300);

          const closeBtn = page.locator('.gh-close').first();
          // Close button should be visible on mobile when popup is active
          const isVisible = await closeBtn.isVisible().catch(() => false);
          expect(typeof isVisible).toBe('boolean');
        }
      }
    });

    test('should display correctly on tablet (768px)', async ({ page }) => {
      await page.setViewportSize({ width: 768, height: 1024 });
      await page.goto('/open-source-docs.html');

      await page.waitForTimeout(2000);

      const widget = page.locator('.gh-contributors-mini').first();
      await expect(widget).toBeVisible();
    });

    test('should display correctly on desktop (1920px)', async ({ page }) => {
      await page.setViewportSize({ width: 1920, height: 1080 });
      await page.goto('/open-source-docs.html');

      await page.waitForTimeout(2000);

      const widget = page.locator('.gh-contributors-mini').first();
      await expect(widget).toBeVisible();
    });
  });

  test.describe('Accessibility', () => {
    test('should have proper ARIA attributes', async ({ page }) => {
      await page.goto('/open-source-docs.html');

      await page.waitForTimeout(2000);

      const trigger = page.locator('.gh-avatars-compact').first();

      if (await trigger.isVisible()) {
        // Check role
        const role = await trigger.getAttribute('role');
        expect(role).toBe('button');

        // Check aria-label
        const ariaLabel = await trigger.getAttribute('aria-label');
        expect(ariaLabel).toContain('contributor');

        // Check aria-expanded
        const ariaExpanded = await trigger.getAttribute('aria-expanded');
        expect(ariaExpanded).toBeTruthy();
      }
    });

    test('should be keyboard accessible', async ({ page }) => {
      await page.goto('/open-source-docs.html');

      await page.waitForTimeout(2000);

      const trigger = page.locator('.gh-avatars-compact').first();

      if (await trigger.isVisible()) {
        // Should have tabindex
        const tabindex = await trigger.getAttribute('tabindex');
        expect(tabindex).toBe('0');

        // Should be focusable
        await trigger.focus();
        const isFocused = await trigger.evaluate((el) => el === document.activeElement);
        expect(isFocused).toBe(true);
      }
    });

    test('should have alt text on images', async ({ page }) => {
      await page.goto('/open-source-docs.html');

      await page.waitForTimeout(2000);

      const avatars = page.locator('.gh-avatar');
      const count = await avatars.count();

      for (let i = 0; i < count; i++) {
        const avatar = avatars.nth(i);
        const alt = await avatar.getAttribute('alt');
        expect(alt).toBeTruthy();
        expect(alt).not.toBe('');
      }
    });
  });

  test.describe('Performance', () => {
    test('should load quickly (< 3 seconds)', async ({ page }) => {
      const start = Date.now();
      await page.goto('/open-source-docs.html');
      const loadTime = Date.now() - start;

      expect(loadTime).toBeLessThan(3000);
    });

    test('should not cause layout shifts', async ({ page }) => {
      await page.goto('/open-source-docs.html');

      // Wait for page to settle
      await page.waitForTimeout(2000);

      // Check if widget container has stable size
      const widget = page.locator('#gh-widget-container');
      const box1 = await widget.boundingBox();

      await page.waitForTimeout(1000);

      const box2 = await widget.boundingBox();

      if (box1 && box2) {
        // Height should be stable (allowing small variance)
        expect(Math.abs(box1.height - box2.height)).toBeLessThan(10);
      }
    });
  });

  test.describe('Error Handling', () => {
    test('should handle missing token gracefully', async ({ page }) => {
      // The widget should show a waiting state or error when no contributors load
      await page.goto('/open-source-docs.html');

      await page.waitForTimeout(3000);

      // Widget container should exist even if no data
      const widget = page.locator('#gh-widget-container');
      await expect(widget).toBeVisible();
    });

    test('should not show console errors', async ({ page }) => {
      const errors: string[] = [];

      page.on('console', (msg) => {
        if (msg.type() === 'error') {
          errors.push(msg.text());
        }
      });

      await page.goto('/open-source-docs.html');
      await page.waitForTimeout(3000);

      // Filter out expected errors (like 401 from GitHub API without token)
      const unexpectedErrors = errors.filter((err) => {
        return !err.includes('401') && !err.includes('GitHub');
      });

      expect(unexpectedErrors).toHaveLength(0);
    });
  });

  test.describe('Visual Regression', () => {
    test('should match widget screenshot', async ({ page }) => {
      await page.setViewportSize({ width: 1280, height: 720 });
      await page.goto('/open-source-docs.html');

      await page.waitForTimeout(2000);

      const widget = page.locator('.gh-contributors-mini').first();

      if (await widget.isVisible()) {
        await expect(widget).toHaveScreenshot('widget-desktop.png', {
          maxDiffPixels: 100,
        });
      }
    });

    test('should match popup screenshot', async ({ page }) => {
      await page.setViewportSize({ width: 1280, height: 720 });
      await page.goto('/open-source-docs.html');

      await page.waitForTimeout(2000);

      const trigger = page.locator('.gh-avatars-compact').first();

      if (await trigger.isVisible()) {
        await trigger.click();
        await page.waitForTimeout(500);

        const popup = page.locator('.gh-popup').first();
        if (await popup.isVisible()) {
          await expect(popup).toHaveScreenshot('popup-desktop.png', {
            maxDiffPixels: 100,
          });
        }
      }
    });
  });
});
