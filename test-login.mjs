import { chromium } from 'playwright';

const browser = await chromium.launch({ headless: false });
const page = await browser.newPage();

try {
  console.log('1. Navegando a http://localhost:3000...');
  await page.goto('http://localhost:3000', { waitUntil: 'networkidle', timeout: 15000 });
  
  console.log('2. URL actual:', page.url());
  console.log('3. Título:', await page.title());

  // Esperar a que cargue
  await page.waitForTimeout(2000);

  // Tomar screenshot de la página de login
  await page.screenshot({ path: 'C:\\Users\\ASUS TUF F15\\Desktop\\hospital\\screenshots\\01-login-page.png', fullPage: true });
  console.log('4. Screenshot login guardado');

  // Buscar campos de email y password
  const emailInput = await page.$('input[type="email"], input[name="email"], input[placeholder*="email"], input[placeholder*="correo"]');
  const passwordInput = await page.$('input[type="password"], input[name="password"]');

  if (emailInput && passwordInput) {
    console.log('5. Campos de login encontrados');
    await emailInput.fill('admin@hospital.bo');
    await passwordInput.fill('password');
    
    await page.screenshot({ path: 'C:\\Users\\ASUS TUF F15\\Desktop\\hospital\\screenshots\\02-login-filled.png', fullPage: true });
    console.log('6. Credenciales填写 completadas');
    
    // Buscar botón de submit
    const submitButton = await page.$('button[type="submit"], button:has-text("Iniciar"), button:has-text("Entrar"), button:has-text("Login")');
    if (submitButton) {
      console.log('7. Botón de login encontrado, clickeando...');
      await submitButton.click();
      
      // Esperar navegación
      await page.waitForTimeout(5000);
      
      console.log('8. URL después de login:', page.url());
      await page.screenshot({ path: 'C:\\Users\\ASUS TUF F15\\Desktop\\hospital\\screenshots\\03-after-login.png', fullPage: true });
      console.log('9. Screenshot post-login guardado');
      
      // Verificar si hay errores en consola
      const errors = [];
      page.on('pageerror', err => errors.push(err.message));
      
      // Esperar un momento más
      await page.waitForTimeout(2000);
      await page.screenshot({ path: 'C:\\Users\\ASUS TUF F15\\Desktop\\hospital\\screenshots\\04-dashboard.png', fullPage: true });
      console.log('10. Screenshot dashboard guardado');
      
      if (errors.length > 0) {
        console.log('ERRORES EN CONSOLA:', errors);
      } else {
        console.log('11. No se detectaron errores JS en consola');
      }
    } else {
      console.log('ERROR: No se encontró botón de submit');
    }
  } else {
    console.log('ERROR: No se encontraron campos de email/password');
    console.log('HTML de la página:', await page.content());
  }

} catch (error) {
  console.log('ERROR:', error.message);
  await page.screenshot({ path: 'C:\\Users\\ASUS TUF F15\\Desktop\\hospital\\screenshots\\error.png', fullPage: true });
} finally {
  await browser.close();
}
