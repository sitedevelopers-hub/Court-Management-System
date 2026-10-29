export default async function run(page, ui) {
  await page.goto('http://127.0.0.1:8000/login');
  await page.locator('#email').fill('qa-test@example.com');
  await page.locator('#password').fill('password123');
  await page.locator('button[type="submit"]').click();
  await page.waitForLoadState('networkidle');
  const url = page.url();
  await page.goto('http://127.0.0.1:8000/dashboard');
  await page.waitForLoadState('networkidle');
  const sidebar = await page.evaluate(() => {
    const sb = document.querySelector('.cms-sidebar');
    if (!sb) return null;
    const r = sb.getBoundingClientRect();
    return { x: r.x, y: r.y, w: r.width, h: r.height, links: sb.querySelectorAll('.cms-link').length, hasLogo: !!sb.querySelector('.cms-logo') };
  });
  const bodyPad = await page.evaluate(() => getComputedStyle(document.body).paddingLeft);
  await page.screenshot({ path: 'C:/Users/KIVUVA/Desktop/Court Management System/storage/dashboard-check.png', fullPage: false });
  return { afterLoginUrl: url, sidebar, bodyPaddingLeft: bodyPad };
}
