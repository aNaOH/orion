import { test, expect } from '@playwright/test';

test('has title', async ({ page }) => {
  await page.goto('/');

  // Expect a title "to contain" a substring.
  // Update this with the actual title of your app
  await expect(page).toHaveTitle(/Orion/i);
});

test('homepage loads', async ({ page }) => {
  await page.goto('/');

  // Verify that some home page content exists
  const body = page.locator('body');
  await expect(body).toBeVisible();
});
