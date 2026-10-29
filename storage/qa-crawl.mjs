// Login then visit every nav route, capturing HTTP status and console errors.
export default async function run(page, ui) {
  const routes = ['cases', 'participants', 'hearings', 'courtrooms', 'court-events', 'case-documents', 'case-fees', 'payments', 'bail-bonds', 'financial-adjustments', 'financial-reports', 'user-management'];
  await page.goto('http://127.0.0.1:8000/login');
  await page.fill('#email', 'qa-test@example.com');
  await page.fill('#password', 'password123');
  await page.click('button[type="submit"]');
  await page.waitForLoadState('networkidle');
  const results = [];
  for (const r of routes) {
    const errors = [];
    page.on('pageerror', e => errors.push(e.message));
    const resp = await page.goto(`http://127.0.0.1:8000/${r}`, { waitUntil: 'networkidle' });
    results.push({ route: r, status: resp.status(), errors });
  }
  return results;
}
