export default async function run(page, ui) {
  await page.goto('http://127.0.0.1:8000/login');
  await page.fill('#email', 'qa-test@example.com');
  await page.fill('#password', 'password123');
  await page.click('button[type="submit"]');
  await page.waitForLoadState('networkidle');
  const dashUrl = page.url();
  const hero = await page.evaluate(() => document.querySelector('.hero h1')?.innerText ?? null);
  const reminders = await page.evaluate(() => document.querySelector('#hearing-reminders')?.innerText?.slice(0, 150) ?? null);
  const userLink = await page.evaluate(() => !!document.querySelector('.cms-link[href*="user-management"]'));
  await page.screenshot({ path: 'C:/Users/KIVUVA/Desktop/Court Management System/storage/dash-v3.png' });

  await page.goto('http://127.0.0.1:8000/participants');
  await page.screenshot({ path: 'C:/Users/KIVUVA/Desktop/Court Management System/storage/participants-v3.png' });
  const bg = await page.evaluate(() => getComputedStyle(document.body).backgroundImage.slice(0, 60));

  return { dashUrl, hero, reminders, userLinkVisibleForQAUser: userLink, participantsBg: bg };
}
