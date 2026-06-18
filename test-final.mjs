import { chromium } from 'playwright';

const browser = await chromium.launch({ headless: false });
const page = await browser.newPage();
const errors = [];
page.on('pageerror', err => errors.push({ url: page.url(), error: err.message.substring(0, 200) }));

try {
  await page.goto('http://localhost:3000/login', { waitUntil: 'networkidle', timeout: 15000 });
  await page.fill('input[type="email"]', 'admin@hospital.bo');
  await page.fill('input[type="password"]', 'password');
  await page.click('button[type="submit"]');
  await page.waitForURL('**/dashboard', { timeout: 10000 });
  console.log('LOGIN OK');

  const sections = [
    { name: 'Dashboard', url: '/dashboard', file: 'final-01-dashboard.png' },
    { name: 'Pacientes', url: '/patients', file: 'final-02-patients.png' },
    { name: 'Nuevo Paciente', url: '/patients/create', file: 'final-03-patient-create.png' },
    { name: 'Hospitalización', url: '/hospitalization', file: 'final-04-hospitalization.png' },
    { name: 'Historiales', url: '/clinical/records', file: 'final-05-clinical.png' },
    { name: 'Citas', url: '/appointments', file: 'final-06-appointments.png' },
    { name: 'Nueva Cita', url: '/appointments/create', file: 'final-07-appointment-create.png' },
    { name: 'SOAP Notes', url: '/soap-notes', file: 'final-08-soap.png' },
    { name: 'Lab Orders', url: '/lab/orders', file: 'final-09-lab-orders.png' },
    { name: 'Lab Results', url: '/lab/results', file: 'final-10-lab-results.png' },
    { name: 'Emergencia', url: '/emergency', file: 'final-11-emergency.png' },
    { name: 'Medicamentos', url: '/pharmacy/medications', file: 'final-12-medications.png' },
    { name: 'Proveedores', url: '/pharmacy/suppliers', file: 'final-13-suppliers.png' },
    { name: 'Órdenes Compra', url: '/pharmacy/purchases', file: 'final-14-purchases.png' },
    { name: 'Inventario', url: '/inventory', file: 'final-15-inventory.png' },
    { name: 'Facturas', url: '/billing/invoices', file: 'final-16-invoices.png' },
    { name: 'Pagos', url: '/billing/payments', file: 'final-17-payments.png' },
    { name: 'Caja', url: '/billing/cash-register', file: 'final-18-cash.png' },
    { name: 'Reportes', url: '/reports', file: 'final-19-reports.png' },
    { name: 'Usuarios', url: '/settings/users', file: 'final-20-users.png' },
    { name: 'Roles', url: '/settings/roles', file: 'final-21-roles.png' },
    { name: 'Configuración', url: '/settings', file: 'final-22-settings.png' },
    { name: 'Auditoría', url: '/audit', file: 'final-23-audit.png' },
    { name: 'Notificaciones', url: '/notifications', file: 'final-24-notifications.png' },
  ];

  for (const section of sections) {
    try {
      await page.goto(`http://localhost:3000${section.url}`, { waitUntil: 'networkidle', timeout: 10000 });
      await page.waitForTimeout(1500);
      await page.screenshot({ path: `C:\\Users\\ASUS TUF F15\\Desktop\\hospital\\screenshots\\${section.file}`, fullPage: true });
      const currentUrl = page.url();
      const redirected = currentUrl.includes('/dashboard') && !section.url.includes('dashboard');
      console.log(`${redirected ? '⚠ REDIRECT' : '✓'} ${section.name} → ${currentUrl}`);
    } catch (e) {
      console.log(`✗ ${section.name} → ${e.message.split('\n')[0]}`);
    }
  }

  // Test interactions: click on a patient
  console.log('\n--- INTERACTION TESTS ---');
  
  // Test patient detail
  await page.goto('http://localhost:3000/patients', { waitUntil: 'networkidle', timeout: 10000 });
  await page.waitForTimeout(1000);
  const firstPatientRow = await page.$('table tbody tr, [class*="cursor-pointer"]');
  if (firstPatientRow) {
    await firstPatientRow.click();
    await page.waitForTimeout(2000);
    await page.screenshot({ path: 'C:\\Users\\ASUS TUF F15\\Desktop\\hospital\\screenshots\\final-25-patient-detail.png', fullPage: true });
    console.log('✓ Patient detail clicked');
  }

  // Test logout
  await page.goto('http://localhost:3000/dashboard', { waitUntil: 'networkidle', timeout: 10000 });
  await page.waitForTimeout(1000);
  const avatarBtn = await page.$('[class*="avatar"], button:has(.rounded-full)');
  if (avatarBtn) {
    await avatarBtn.click();
    await page.waitForTimeout(500);
    await page.screenshot({ path: 'C:\\Users\\ASUS TUF F15\\Desktop\\hospital\\screenshots\\final-26-user-menu.png', fullPage: true });
    console.log('✓ User menu opened');
  }

  console.log(`\n--- RESUMEN ---`);
  console.log(`JS Errors: ${errors.length}`);
  for (const err of errors) console.log(`  ${err.url} → ${err.error}`);

} finally {
  await browser.close();
}
