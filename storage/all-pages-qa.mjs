// QA: login then visit every module page, capture errors
export default async function (page, ctx) {
  const results = [];
  await page.goto('http://127.0.0.1:8000/login');
  await page.fill('input[name="email"]', 'qa-test@example.com');
  await page.fill('input[name="password"]', 'password123');
  await page.click('button[type="submit"]');
  await page.waitForURL('**/dashboard');

  const pages = [
    'cases', 'participants', 'hearings', 'courtrooms', 'court-events',
    'case-documents', 'case-fees', 'payments', 'bail-bonds',
    'financial-adjustments', 'financial-reports', 'user-management'
  ];
  for (const p of pages) {
    await page.goto('http://127.0.0.1:8000/' + p);
    await page.waitForTimeout(400);
    const title = await page.title();
    const hasError = await page.evaluate(() => {
      const t = document.body.innerText;
      return /Whoops|Error|Exception|500/.test(t.slice(0, 500));
    });
    results.push({ page: p, title, hasError });
  }
  return JSON.stringify(results, null, 1);
}
