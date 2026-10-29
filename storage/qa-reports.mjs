export default async function run(page, ui) {
  const results = {};
  await page.goto('http://127.0.0.1:8000/reports');
  await page.waitForTimeout(800);
  results.url = page.url();

  // Generate a report
  const hasForm = await page.locator('input[name="title"]').count();
  results.hasForm = hasForm > 0;
  if (hasForm > 0) {
    await page.locator('input[name="title"]').fill('QA Monthly Revenue Report');
    await page.locator('select[name="report_type"]').selectOption('financial');
    await page.locator('form[action*="generate"] button[type="submit"]').click();
    await page.waitForTimeout(1500);
    results.urlAfterGenerate = page.url();
    results.flash = (await page.locator('[class*="alert"], [class*="success"]').allInnerTexts().catch(() => [])).join(' | ').slice(0, 250);
    results.rows = await page.locator('table tbody tr').count();
  }

  // Open the share dialog
  const shareBtn = page.locator('button:has-text("Share")').first();
  if (await shareBtn.count()) {
    await shareBtn.click();
    await page.waitForTimeout(500);
    results.dialogOpen = await page.locator('#shareDialog[open]').count() > 0;
    results.recipientCount = await page.locator('#shareDialog input[name="recipients[]"]').count();
    if (results.recipientCount > 0) {
      await page.locator('#shareDialog input[name="recipients[]"]').first().check();
      await page.locator('#shareDialog button[type="submit"]').click();
      await page.waitForTimeout(1500);
      results.flashAfterShare = (await page.locator('[class*="alert"], [class*="success"]').allInnerTexts().catch(() => [])).join(' | ').slice(0, 250);
    }
  }

  await page.screenshot({ path: 'storage/reports-v4.png', fullPage: true });
  return results;
}
