export default async function run(page, ui) {
  const results = {};
  // Login
  await page.goto('http://127.0.0.1:8000/login');
  await page.getByLabel(/email/i).fill('qa-test@example.com');
  await page.getByLabel(/password/i).fill('password123');
  await page.getByRole('button', { name: /sign in/i }).click();
  await page.waitForTimeout(1500);
  results.afterLoginUrl = page.url();

  // Screenshot dashboard
  await page.goto('http://127.0.0.1:8000/dashboard');
  await page.waitForTimeout(800);
  await page.screenshot({ path: 'storage/dash-v4.png', fullPage: true });
  results.dashText = (await page.textContent('body'))?.slice(0, 400);

  // Module pages
  const routes = ['cases', 'participants', 'hearings', 'courtrooms', 'court-events', 'case-documents', 'case-fees', 'payments', 'bail-bonds', 'financial-adjustments', 'financial-reports', 'reports', 'work-templates'];
  results.modules = {};
  for (const r of routes) {
    const resp = await page.goto(`http://127.0.0.1:8000/${r}`);
    await page.waitForTimeout(500);
    const text = (await page.textContent('body'))?.slice(0, 150);
    results.modules[r] = { status: resp.status(), text };
  }
  return results;
}
