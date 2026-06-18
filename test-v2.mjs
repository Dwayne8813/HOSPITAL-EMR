import { chromium } from 'playwright';

const browser = await chromium.launch({ headless: false });
const page = await browser.newPage();
const errors = [];
page.on('pageerror', err => errors.push({ url: page.url(), error: err.message.substring(0, 150) }));

try {
  // Login
  await page.goto('http://localhost:3000/login', { waitUntil: 'networkidle', timeout: 15000 });
  await page.fill('input[type="email"]', 'admin@hospital.bo');
  await page.fill('input[type="password"]', 'password');
  await page.click('button[type="submit"]');
  await page.waitForURL('**/dashboard', { timeout: 10000 });
  console.log('LOGIN OK');

  const sections = [
    { name: 'Dashboard', url: '/dashboard', file: 'v2-01-dashboard.png' },
    { name: 'Pacientes', url: '/patients', file: 'v2-02-patients.png' },
    { name: 'Hospitalización', url: '/hospitalization', file: 'v2-03-hospitalization.png' },
    { name: 'Historiales Clínicos', url: '/clinical/records', file: 'v2-04-clinical-records.png' },
    { name: 'Citas', url: '/appointments', file: 'v2-05-appointments.png' },
    { name: 'Órdenes Laboratorio', url: '/lab/orders', file: 'v2-06-lab-orders.png' },
    { name: 'Resultados Laboratorio', url: '/lab/results', file: 'v2-07-lab-results.png' },
    { name: 'Emergencia/Triage', url: '/emergency', file: 'v2-08-emergency.png' },
    { name: 'Medicamentos', url: '/pharmacy/medications', file: 'v2-09-medications.png' },
    { name: 'Proveedores', url: '/pharmacy/suppliers', file: 'v2-10-suppliers.png' },
    { name: 'Órdenes Compra', url: '/pharmacy/purchases', file: 'v2-11-purchases.png' },
    { name: 'Facturas', url: '/billing/invoices', file: 'v2-12-invoices.png' },
    { name: 'Pagos', url: '/billing/payments', file: 'v2-13-payments.png' },
    { name: 'Reportes', url: '/reports', file: 'v2-14-reports.png' },
    { name: 'Configuración', url: '/settings', file: 'v2-15-settings.png' },
    { name: 'Usuarios', url: '/settings/users', file: 'v2-16-users.png' },
  ];

  for (const section of sections) {
    try {
      await page.goto(`http://localhost:3000${section.url}`, { waitUntil: 'networkidle', timeout: 10000 });
      await page.waitForTimeout(1500);
      await page.screenshot({ path: `C:\\Users\\ASUS TUF F15\\Desktop\\hospital\\screenshots\\${section.file}`, fullPage: true });
      const h1 = await page.$eval('h1, h2', el => el.textContent?.trim()).catch(() => null);
      const currentUrl = page.url();
      const onDashboard = currentUrl.endsWith('/dashboard') && !section.url.includes('dashboard');
      console.log(`${onDashboard ? '⚠ REDIRECT' : '✓'} ${section.name} → ${currentUrl} | ${h1 || '(no title)'}`);
    } catch (e) {
      console.log(`✗ ${section.name} → ${e.message.split('\n')[0]}`);
    }
  }

  console.log(`\nErrores JS: ${errors.length}`);
  for (const err of errors) {
    console.log(`  ${err.url} → ${err.error}`);
  }
} finally {
  await browser.close();
}
