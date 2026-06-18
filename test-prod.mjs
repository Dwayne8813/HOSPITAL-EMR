import { chromium } from 'playwright';

const browser = await chromium.launch({ headless: false });
const page = await browser.newPage();
const errors = [];
page.on('pageerror', err => errors.push(err.message.substring(0, 150)));

try {
  await page.goto('http://localhost:3000/login', { waitUntil: 'networkidle', timeout: 15000 });
  await page.fill('input[type="email"]', 'admin@hospital.bo');
  await page.fill('input[type="password"]', 'password');
  await page.click('button[type="submit"]');
  await page.waitForURL('**/dashboard', { timeout: 10000 });
  console.log('LOGIN OK');

  // Test Institution Settings
  await page.goto('http://localhost:3000/settings/institution', { waitUntil: 'networkidle', timeout: 10000 });
  await page.waitForTimeout(2000);
  await page.screenshot({ path: 'C:\\Users\\ASUS TUF F15\\Desktop\\hospital\\screenshots\\prod-01-institution.png', fullPage: true });
  console.log('✓ Institution Settings');

  // Test Invoice Create
  await page.goto('http://localhost:3000/billing/invoices/create', { waitUntil: 'networkidle', timeout: 10000 });
  await page.waitForTimeout(2000);
  await page.screenshot({ path: 'C:\\Users\\ASUS TUF F15\\Desktop\\hospital\\screenshots\\prod-02-invoice-create.png', fullPage: true });
  console.log('✓ Invoice Create');

  // Test Invoice List
  await page.goto('http://localhost:3000/billing/invoices', { waitUntil: 'networkidle', timeout: 10000 });
  await page.waitForTimeout(2000);
  await page.screenshot({ path: 'C:\\Users\\ASUS TUF F15\\Desktop\\hospital\\screenshots\\prod-03-invoices.png', fullPage: true });
  console.log('✓ Invoice List');

  // Test Medications with fixed price
  await page.goto('http://localhost:3000/pharmacy/medications', { waitUntil: 'networkidle', timeout: 10000 });
  await page.waitForTimeout(2000);
  await page.screenshot({ path: 'C:\\Users\\ASUS TUF F15\\Desktop\\hospital\\screenshots\\prod-04-medications.png', fullPage: true });
  console.log('✓ Medications');

  // Test Settings page
  await page.goto('http://localhost:3000/settings', { waitUntil: 'networkidle', timeout: 10000 });
  await page.waitForTimeout(1000);
  await page.screenshot({ path: 'C:\\Users\\ASUS TUF F15\\Desktop\\hospital\\screenshots\\prod-05-settings.png', fullPage: true });
  console.log('✓ Settings');

  console.log(`\nJS Errors: ${errors.length}`);
  for (const err of errors) console.log(`  ${err}`);
} finally {
  await browser.close();
}
