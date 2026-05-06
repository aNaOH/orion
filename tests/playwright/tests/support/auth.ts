import { expect, Page } from '@playwright/test';

export async function loginAs(page: Page, email: string, password: string) {
  await page.goto('/login');
  await page.fill('#email', email);
  await page.fill('#password', password);
  await page.click('#submitButton');
  await page.waitForURL('**/');
  await expect(page.locator('body')).toContainText(/Cuenta|Perfil|Inicio|Orion/i);
}
