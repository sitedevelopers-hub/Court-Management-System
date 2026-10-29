// Register a fresh account through the real signup flow, then verify the dashboard.
export default async function run(page, ui) {
  await page.goto('http://127.0.0.1:8000/register');
  await page.waitForTimeout(300);

  const email = 'qauser' + Date.now() + '@example.com';
  await page.fill('#name', 'Priscillah Kivuva');
  await page.fill('#email', email);
  await page.fill('#password', 'securepass123');
  await page.fill('#password_confirmation', 'securepass123');
  await page.click('button[type="submit"]');
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(600);

  const afterRegisterUrl = page.url();

  // Screenshot the dashboard after signup (should auto-login and land on it)
  await page.screenshot({ path: 'C:/Users/KIVUVA/Desktop/Court Management System/storage/dashboard-after-signup.png', fullPage: true });

  const heroText = await page.evaluate(() => document.querySelector('.hero h1')?.innerText ?? null);
  const welcomeFlash = await page.evaluate(() => document.querySelector('.success-msg, .alert, [class*=success]')?.innerText ?? null);

  return { afterRegisterUrl, heroText, welcomeFlash };
}
