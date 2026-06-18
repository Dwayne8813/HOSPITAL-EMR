import { chromium } from 'playwright';

const browser = await chromium.launch({ headless: false });
const page = await browser.newPage();
const errors = [];
page.on('pageerror', err => errors.push({ url: page.url(), error: err.message.substring(0, 150) }));

try {
  await page.goto('http://localhost:3000/login', { waitUntil: 'networkidle', timeout: 15000 });
  await page.fill('input[type="email"]', 'admin@hospital.bo');
  await page.fill('input[type="password"]', 'password');
  await page.click('button[type="submit"]');
  await page.waitForURL('**/dashboard', { timeout: 10000 });
  console.log('LOGIN OK');
  await page.waitForTimeout(2000);

  const sections = [
    { name: 'Dashboard', url: '/dashboard', file: 'design-01-dashboard.png' },
    { name: 'Pacientes', url: '/patients', file: 'design-02-patients.png' },
    { name: 'Citas', url: '/appointments', file: 'design-03-appointments.png' },
    { name: 'Medicamentos', url: '/pharmacy/medications', file: 'design-04-medications.png' },
    { name: 'Proveedores', url: '/pharmacy/suppliers', file: 'design-05-suppliers.png' },
    { name: 'Usuarios', url: '/settings/users', file: 'design-06-users.png' },
    { name: 'Facturas', url: '/billing/invoices', file: 'design-07-invoices.png' },
    { name: 'Reportes', url: '/reports', file: 'design-08-reports.png' },
  ];

  for (const section of sections) {
    await page.goto(`http://localhost:3000${section.url}`, { waitUntil: 'networkidle', timeout: 10000 });
    await page.waitForTimeout(2000);
    await page.screenshot({ path: `C:\\Users\\ASUS TUF F15\\Desktop\\hospital\\screenshots\\${section.file}`, fullPage: true });
    console.log(`✓ ${section.name}`);
  }

  console.log(`\nErrores JS: ${errors.length}`);
  for (const err of errors) console.log(`  ${err.url} → ${err.error}`);
} finally {
  await browser.close();
}
