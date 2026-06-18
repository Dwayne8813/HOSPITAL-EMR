import { chromium } from 'playwright';

const browser = await chromium.launch({ headless: false });
const page = await browser.newPage();
const errors = [];
page.on('pageerror', err => errors.push({ url: page.url(), error: err.message }));

try {
  // Login
  console.log('--- LOGIN ---');
  await page.goto('http://localhost:3000/login', { waitUntil: 'networkidle', timeout: 15000 });
  await page.fill('input[type="email"]', 'admin@hospital.bo');
  await page.fill('input[type="password"]', 'password');
  await page.click('button[type="submit"]');
  await page.waitForURL('**/dashboard', { timeout: 10000 });
  console.log('OK: Login → Dashboard');
  await page.screenshot({ path: 'C:\\Users\\ASUS TUF F15\\Desktop\\hospital\\screenshots\\final-01-dashboard.png' });

  const sections = [
    { name: 'Pacientes', url: '/patients', file: 'final-02-patients.png' },
    { name: 'Hospitalización', url: '/hospitalization', file: 'final-03-hospitalization.png' },
    { name: 'Historiales', url: '/medical-records', file: 'final-04-medical-records.png' },
    { name: 'SOAP', url: '/soap-notes', file: 'final-05-soap-notes.png' },
    { name: 'Citas', url: '/appointments', file: 'final-06-appointments.png' },
    { name: 'Laboratorio', url: '/laboratory', file: 'final-07-laboratory.png' },
    { name: 'Emergencia', url: '/emergency', file: 'final-08-emergency.png' },
    { name: 'Órdenes', url: '/orders', file: 'final-09-orders.png' },
    { name: 'Medicamentos', url: '/medications', file: 'final-10-medications.png' },
    { name: 'Proveedores', url: '/suppliers', file: 'final-11-suppliers.png' },
    { name: 'Facturación', url: '/billing', file: 'final-12-billing.png' },
    { name: 'Reportes', url: '/reports', file: 'final-13-reports.png' },
  ];

  for (const section of sections) {
    console.log(`--- ${section.name} (${section.url}) ---`);
    try {
      await page.goto(`http://localhost:3000${section.url}`, { waitUntil: 'networkidle', timeout: 10000 });
      await page.waitForTimeout(1500);
      await page.screenshot({ path: `C:\\Users\\ASUS TUF F15\\Desktop\\hospital\\screenshots\\${section.file}`, fullPage: true });
      const title = await page.textContent('h1, h2, .page-title, [class*="title"]').catch(() => null);
      console.log(`  OK: ${title ? title.trim() : '(sin título)'}`);
    } catch (e) {
      console.log(`  ERROR: ${e.message.split('\n')[0]}`);
    }
  }

  // Resumen de errores JS
  console.log(`\n--- RESUMEN: ${errors.length} errores JS detectados ---`);
  for (const err of errors) {
    console.log(`  ${err.url} → ${err.error.substring(0, 120)}`);
  }

} catch (error) {
  console.log('FATAL:', error.message);
} finally {
  await browser.close();
}
