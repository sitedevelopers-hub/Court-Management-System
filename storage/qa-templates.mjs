export default async function run(page, ui) {
  const results = {};
  await page.goto('http://127.0.0.1:8000/work-templates');
  await page.waitForTimeout(800);
  results.url = page.url();

  // If bounced to login, log in
  if (page.url().includes('/login')) {
    await page.locator('input[name="email"]').fill('qa-test@example.com');
    await page.locator('input[name="password"]').fill('password123');
    await page.locator('form[action*="login"] button[type="submit"], main form button[type="submit"]').first().click();
    await page.waitForTimeout(1500);
    await page.goto('http://127.0.0.1:8000/work-templates');
    await page.waitForTimeout(800);
    results.urlAfterLogin = page.url();
  }

  results.bodyStart = (await page.textContent('body'))?.slice(0, 300);

  const form = await page.locator('textarea[name="content"]').count();
  results.hasForm = form > 0;
  if (form > 0) {
    await page.locator('input[name="title"]').fill('Test Ruling Template');
    await page.locator('textarea[name="content"]').fill('IN THE MAGISTRATES COURT AT NAIROBI\n\nREPUBLIC vs JOHN DOE\n\nRULING:\nThe accused is hereby released on bond.');
    await page.locator('form[action*="work-templates"]').first().locator('button[type="submit"]').click();
    await page.waitForTimeout(1500);
    results.urlAfterSave = page.url();
    results.rows = await page.locator('table tbody tr').count();
    results.flash = (await page.locator('[class*="alert"], [class*="success"], [class*="flash"]').allInnerTexts().catch(() => [])).join(' | ').slice(0, 300);
  }
  await page.screenshot({ path: 'storage/templates-v4.png', fullPage: true });
  return results;
}
