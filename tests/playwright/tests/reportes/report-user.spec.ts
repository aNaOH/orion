import { expect, test } from '@playwright/test';
import { loginAs } from '../support/auth';
import { TEST_USERS } from '../support/users';

async function expectDialog(page, expectedText: RegExp) {
  const dialog = await page.waitForEvent('dialog');
  expect(dialog.message()).toMatch(expectedText);
  await dialog.accept();
}

test.describe('namespace: reportes', () => {
  test('carga el formulario de reporte para otro usuario', async ({ page }) => {
    await loginAs(page, TEST_USERS.reporterForm.email, TEST_USERS.reporterForm.password);
    await page.goto(`/support/report/user/${TEST_USERS.targetSpam.id}`);

    await expect(page.locator('h1')).toContainText('Reportar Usuario');
    await expect(page.locator('#report-form')).toBeVisible();
    await expect(page.locator('input[name="reported_id"]')).toHaveValue(String(TEST_USERS.targetSpam.id));
  });

  test('valida en cliente que el motivo sea obligatorio', async ({ page }) => {
    await loginAs(page, TEST_USERS.reporterValidation.email, TEST_USERS.reporterValidation.password);
    await page.goto(`/support/report/user/${TEST_USERS.targetAvatar.id}`);
    await page.fill('#description', 'Descripcion suficientemente larga para que falle solo el motivo.');

    const dialogPromise = page.waitForEvent('dialog');
    await page.click('#submit-btn');
    const dialog = await dialogPromise;
    expect(dialog.message()).toMatch(/selecciona un motivo/i);
    await dialog.accept();
  });

  test('valida en cliente que la descripcion tenga longitud minima', async ({ page }) => {
    await loginAs(page, TEST_USERS.reporterValidation.email, TEST_USERS.reporterValidation.password);
    await page.goto(`/support/report/user/${TEST_USERS.targetAvatar.id}`);
    await page.selectOption('#reason', 'avatar');
    await page.fill('#description', 'corto');

    const dialogPromise = page.waitForEvent('dialog');
    await page.click('#submit-btn');
    const dialog = await dialogPromise;
    expect(dialog.message()).toMatch(/descripci[oó]n m[aí]s detallada/i);
    await dialog.accept();
  });

  test('bloquea el autoreporte en backend', async ({ page }) => {
    await loginAs(page, TEST_USERS.reporterSelf.email, TEST_USERS.reporterSelf.password);
    await page.goto(`/support/report/user/${TEST_USERS.reporterSelf.id}`);
    await page.selectOption('#reason', 'spam');
    await page.fill('#description', 'Intento de autoreporte desde el usuario de pruebas.');
    const dialogPromise = expectDialog(page, /no puedes reportarte a ti mismo/i);
    await page.click('#submit-btn');
    await dialogPromise;
  });

  test('envia un reporte valido y redirige al perfil reportado', async ({ page }) => {
    await loginAs(page, TEST_USERS.reporterForm.email, TEST_USERS.reporterForm.password);
    await page.goto(`/support/report/user/${TEST_USERS.targetSpam.id}`);
    await page.selectOption('#reason', 'spam');
    await page.fill('#description', 'Este usuario está publicando mensajes repetitivos y claramente no solicitados.');
    const dialogPromise = expectDialog(page, /reporte ha sido enviado correctamente/i);
    await page.click('#submit-btn');
    await dialogPromise;
    await page.waitForURL(`**/user/${TEST_USERS.targetSpam.id}`);
  });

  test('bloquea reportes duplicados pendientes', async ({ page }) => {
    await loginAs(page, TEST_USERS.reporterDuplicate.email, TEST_USERS.reporterDuplicate.password);
    await page.goto(`/support/report/user/${TEST_USERS.targetIdentity.id}`);
    await page.selectOption('#reason', 'impersonation');
    await page.fill('#description', 'Este usuario parece intentar hacerse pasar por una cuenta oficial.');
    let dialogPromise = expectDialog(page, /reporte ha sido enviado correctamente/i);
    await page.click('#submit-btn');
    await dialogPromise;
    await page.waitForURL(`**/user/${TEST_USERS.targetIdentity.id}`);

    await page.goto(`/support/report/user/${TEST_USERS.targetIdentity.id}`);
    await page.selectOption('#reason', 'impersonation');
    await page.fill('#description', 'Segundo intento del mismo reporte para comprobar el bloqueo por duplicado.');
    dialogPromise = expectDialog(page, /ya tienes un reporte pendiente/i);
    await page.click('#submit-btn');
    await dialogPromise;
  });

  test('permite a un admin abrir el ticket mas reciente', async ({ browser, page }) => {
    await loginAs(page, TEST_USERS.reporterAdminFlow.email, TEST_USERS.reporterAdminFlow.password);
    await page.goto(`/support/report/user/${TEST_USERS.targetAvatar.id}`);
    await page.selectOption('#reason', 'avatar');
    await page.fill('#description', 'La imagen de perfil de este usuario requiere revisión por incumplir las normas.');
    const dialogPromise = expectDialog(page, /reporte ha sido enviado correctamente/i);
    await page.click('#submit-btn');
    await dialogPromise;

    const adminPage = await browser.newPage();
    await loginAs(adminPage, TEST_USERS.admin.email, TEST_USERS.admin.password);
    await adminPage.goto('/admin/tickets');
    await adminPage.locator('a[href^="/admin/tickets/"]').first().click();

    await expect(adminPage.locator('h1')).toContainText('Ticket #');
    await expect(adminPage.locator('body')).toContainText(/report_user|Contenido del Reporte/i);
  });
});
