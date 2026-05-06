import { expect, test } from '@playwright/test';

test('homepage loads', async ({ page }) => {
  await page.goto('/');
  await expect(page).toHaveTitle(/Orion/i);
  await expect(page.locator('body')).toBeVisible();
});
