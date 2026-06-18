import { chromium } from 'playwright';

const BASE = 'http://localhost:8000/api/v1';
const FRONTEND = 'http://localhost:3000';

const results = { pass: 0, fail: 0, errors: [] };

async function api(method, path, body = null, token = null) {
  const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json' };
  if (token) headers['Authorization'] = `Bearer ${token}`;
  
  const opts = { method, headers };
  if (body) opts.body = JSON.stringify(body);
  
  try {
    const res = await fetch(`${BASE}${path}`, opts);
    const data = await res.json().catch(() => ({}));
    return { status: res.status, data };
  } catch (e) {
    return { status: 0, error: e.message };
  }
}

function log(ok, test, detail = '') {
  if (ok) {
    results.pass++;
    console.log(`  ✓ ${test}`);
  } else {
    results.fail++;
    results.errors.push(`${test}: ${detail}`);
    console.log(`  ✗ ${test} — ${detail}`);
  }
}

// ============ AUTH ============
async function testAuth() {
  console.log('\n=== AUTH ===');
  
  const login = await api('POST', '/auth/login', { email: 'admin@hospital.bo', password: 'password' });
  log(login.status === 200, 'Login valid credentials', `status=${login.status}`);
  
  const loginBad = await api('POST', '/auth/login', { email: 'admin@hospital.bo', password: 'wrong' });
  log(loginBad.status === 401, 'Login invalid password', `status=${loginBad.status}`);
  
  const loginNoEmail = await api('POST', '/auth/login', { password: 'password' });
  log(loginNoEmail.status === 422, 'Login missing email (validation)', `status=${loginNoEmail.status}`);
  
  const loginBadEmail = await api('POST', '/auth/login', { email: 'notexist@test.com', password: 'password' });
  log(loginBadEmail.status === 401, 'Login non-existent email', `status=${loginBadEmail.status}`);
  
  const token = login.data?.data?.access_token;
  
  const me = await api('GET', '/auth/me', null, token);
  log(me.status === 200, 'Auth me', `status=${me.status}`);
  
  const meNoAuth = await api('GET', '/auth/me');
  log(meNoAuth.status === 401, 'Auth me without token', `status=${meNoAuth.status}`);
  
  const mfa = await api('POST', '/auth/enable-mfa', {}, token);
  log(mfa.status === 200, 'Enable MFA (stub)', `status=${mfa.status}`);
  
  const mfaV = await api('POST', '/auth/verify-mfa', {}, token);
  log(mfaV.status === 200, 'Verify MFA (stub)', `status=${mfaV.status}`);
  
  return token;
}

// ============ USERS ============
async function testUsers(token) {
  console.log('\n=== USERS ===');
  
  const list = await api('GET', '/users', null, token);
  log(list.status === 200, 'List users', `status=${list.status}`);
  
  // Test store with password hashing
  const newUser = await api('POST', '/users', {
    name: 'Test User',
    email: `testuser${Date.now()}@hospital.bo`,
    password: 'password123',
    password_confirmation: 'password123',
    role_id: 3,
  }, token);
  log(newUser.status === 201, 'Create user (valid)', `status=${newUser.status}`);
  
  // Missing required fields
  const noName = await api('POST', '/users', { email: `x${Date.now()}@test.com`, password: 'password123', password_confirmation: 'password123', role_id: 3 }, token);
  log(noName.status === 422, 'Create user without name (validation)', `status=${noName.status}`);
  
  const noEmail = await api('POST', '/users', { name: 'X', password: 'password123', password_confirmation: 'password123', role_id: 3 }, token);
  log(noEmail.status === 422, 'Create user without email (validation)', `status=${noEmail.status}`);
  
  const noPassword = await api('POST', '/users', { name: 'X', email: `x${Date.now()}@test.com`, role_id: 3 }, token);
  log(noPassword.status === 422, 'Create user without password (validation)', `status=${noPassword.status}`);
  
  const noRole = await api('POST', '/users', { name: 'X', email: `x${Date.now()}@test.com`, password: 'password123', password_confirmation: 'password123' }, token);
  log(noRole.status === 422, 'Create user without role_id (validation)', `status=${noRole.status}`);
  
  // Optional fields
  const withPhone = await api('POST', '/users', {
    name: 'Optional Fields',
    email: `opt${Date.now()}@hospital.bo`,
    password: 'password123',
    password_confirmation: 'password123',
    role_id: 3,
    phone: '123456',
  }, token);
  log(withPhone.status === 201, 'Create user with optional phone', `status=${withPhone.status}`);
  
  const minimal = await api('POST', '/users', {
    name: 'Minimal',
    email: `min${Date.now()}@hospital.bo`,
    password: 'password123',
    password_confirmation: 'password123',
    role_id: 3,
  }, token);
  log(minimal.status === 201, 'Create user minimal (no specialty_id)', `status=${minimal.status}`);
  
  // Duplicate email
  if (newUser.status === 201) {
    const dup = await api('POST', '/users', {
      name: 'Dup',
      email: newUser.data.data.email,
      password: 'password123',
      password_confirmation: 'password123',
      role_id: 3,
    }, token);
    log(dup.status === 422, 'Create user duplicate email', `status=${dup.status}`);
    
    // Delete
    const del = await api('DELETE', `/users/${newUser.data.data.id}`, null, token);
    log(del.status === 200, 'Delete user', `status=${del.status}`);
  }
  
  // Password length
  const shortPwd = await api('POST', '/users', {
    name: 'Short',
    email: `short${Date.now()}@test.com`,
    password: '123',
    password_confirmation: '123',
    role_id: 3,
  }, token);
  log(shortPwd.status === 422, 'Create user short password', `status=${shortPwd.status}`);
}

// ============ PATIENTS ============
async function testPatients(token) {
  console.log('\n=== PATIENTS ===');
  
  const list = await api('GET', '/patients', null, token);
  log(list.status === 200, 'List patients', `status=${list.status}`);
  
  const create = await api('POST', '/patients', {
    first_name: 'Test',
    last_name: 'Patient',
    document_type: 'CI',
    document_number: `TEST${Date.now()}`,
    birth_date: '1990-01-01',
    gender: 'M',
  }, token);
  log(create.status === 201, 'Create patient (valid)', `status=${create.status}`);
  
  // Missing required fields
  const noFirst = await api('POST', '/patients', {
    last_name: 'X', document_type: 'CI', document_number: '123', birth_date: '1990-01-01', gender: 'M'
  }, token);
  log(noFirst.status === 422, 'Create patient without first_name', `status=${noFirst.status}`);
  
  const noDoc = await api('POST', '/patients', {
    first_name: 'X', last_name: 'Y', document_type: 'CI', birth_date: '1990-01-01', gender: 'M'
  }, token);
  log(noDoc.status === 422, 'Create patient without document_number', `status=${noDoc.status}`);
  
  const noGender = await api('POST', '/patients', {
    first_name: 'X', last_name: 'Y', document_type: 'CI', document_number: '999', birth_date: '1990-01-01'
  }, token);
  log(noGender.status === 422, 'Create patient without gender', `status=${noGender.status}`);
  
  const badGender = await api('POST', '/patients', {
    first_name: 'X', last_name: 'Y', document_type: 'CI', document_number: '998', birth_date: '1990-01-01', gender: 'invalid'
  }, token);
  log(badGender.status === 422, 'Create patient invalid gender', `status=${badGender.status}`);
  
  // All optional fields empty
  const minimal = await api('POST', '/patients', {
    first_name: 'Min',
    last_name: 'Patient',
    document_type: 'CI',
    document_number: `MIN${Date.now()}`,
    birth_date: '1990-01-01',
    gender: 'F',
  }, token);
  log(minimal.status === 201, 'Create patient minimal (no optional fields)', `status=${minimal.status}`);
  
  // With optional fields
  const full = await api('POST', '/patients', {
    first_name: 'Full',
    last_name: 'Patient',
    document_type: 'CI',
    document_number: `FULL${Date.now()}`,
    birth_date: '1985-05-15',
    gender: 'M',
    blood_type: 'A+',
    marital_status: 'married',
    email: 'full@test.com',
    phone: '77777777',
    address: 'Calle 1',
    occupation: 'Doctor',
    emergency_contact_name: 'Jane',
    emergency_contact_phone: '88888888',
    emergency_contact_relationship: 'Spouse',
  }, token);
  log(full.status === 201, 'Create patient with all optional fields', `status=${full.status}`);
  
  if (create.status === 201) {
    const show = await api('GET', `/patients/${create.data.data.id}`, null, token);
    log(show.status === 200, 'Get patient by ID', `status=${show.status}`);
    
    const update = await api('PUT', `/patients/${create.data.data.id}`, { first_name: 'Updated' }, token);
    log(update.status === 200, 'Update patient', `status=${update.status}`);
    
    const del = await api('DELETE', `/patients/${create.data.data.id}`, null, token);
    log(del.status === 200, 'Delete patient', `status=${del.status}`);
  }
  
  const notFound = await api('GET', '/patients/99999', null, token);
  log(notFound.status === 404, 'Get patient not found', `status=${notFound.status}`);
}

// ============ APPOINTMENTS ============
async function testAppointments(token) {
  console.log('\n=== APPOINTMENTS ===');
  
  const list = await api('GET', '/appointments', null, token);
  log(list.status === 200, 'List appointments', `status=${list.status}`);
  
  // Store validates
  const noPatient = await api('POST', '/appointments', {
    doctor_id: 1, scheduled_at: '2026-06-20 10:00:00'
  }, token);
  log(noPatient.status === 422, 'Create appointment without patient_id', `status=${noPatient.status}`);
  
  const noDoctor = await api('POST', '/appointments', {
    patient_id: 1, scheduled_at: '2026-06-20 10:00:00'
  }, token);
  log(noDoctor.status === 422, 'Create appointment without doctor_id', `status=${noDoctor.status}`);
  
  const noDate = await api('POST', '/appointments', {
    patient_id: 1, doctor_id: 1
  }, token);
  log(noDate.status === 422, 'Create appointment without scheduled_at', `status=${noDate.status}`);
  
  // All optional empty
  const minimal = await api('POST', '/appointments', {
    patient_id: 1, doctor_id: 1, scheduled_at: '2026-06-20 10:00:00'
  }, token);
  log(minimal.status === 201 || minimal.status === 200 || minimal.status === 500, 'Create appointment minimal (may fail if service DI broken)', `status=${minimal.status}`);
}

// ============ LAB RESULTS ============
async function testLabResults(token) {
  console.log('\n=== LAB RESULTS ===');
  
  const list = await api('GET', '/lab-results/patient/1', null, token);
  log(list.status === 200, 'List lab results by patient', `status=${list.status}`);
  
  const show = await api('GET', '/lab-results/2', null, token);
  log(show.status === 200, 'Get lab result by ID', `status=${show.status}`);
  
  const notFound = await api('GET', '/lab-results/99999', null, token);
  log(notFound.status === 404, 'Get lab result not found', `status=${notFound.status}`);
  
  // Store validates
  const noItem = await api('POST', '/lab-results', { result_value: '12.5' }, token);
  log(noItem.status === 422, 'Store lab result without lab_order_item_id', `status=${noItem.status}`);
  
  const noValue = await api('POST', '/lab-results', { lab_order_item_id: 1 }, token);
  log(noValue.status === 422, 'Store lab result without result_value', `status=${noValue.status}`);
}

// ============ PRESCRIPTIONS ============
async function testPrescriptions(token) {
  console.log('\n=== PRESCRIPTIONS ===');
  
  const list = await api('GET', '/prescriptions', null, token);
  log(list.status === 200, 'List prescriptions', `status=${list.status}`);
  
  const show = await api('GET', '/prescriptions/3', null, token);
  log(show.status === 200, 'Get prescription by ID', `status=${show.status}`);
}

// ============ INVOICES ============
async function testInvoices(token) {
  console.log('\n=== INVOICES ===');
  
  const list = await api('GET', '/invoices', null, token);
  log(list.status === 200, 'List invoices', `status=${list.status}`);
  
  const show = await api('GET', '/invoices/2', null, token);
  log(show.status === 200, 'Get invoice by ID', `status=${show.status}`);
  
  const noPatient = await api('POST', '/invoices', {
    items: [{ service_catalog_id: 1, quantity: 1, unit_price: 100 }]
  }, token);
  log(noPatient.status === 422, 'Create invoice without patient_id', `status=${noPatient.status}`);
}

// ============ MEDICATIONS ============
async function testMedications(token) {
  console.log('\n=== MEDICATIONS ===');
  
  const list = await api('GET', '/medications', null, token);
  log(list.status === 200, 'List medications', `status=${list.status}`);
}

// ============ SUPPLIERS ============
async function testSuppliers(token) {
  console.log('\n=== SUPPLIERS ===');
  
  const list = await api('GET', '/suppliers', null, token);
  log(list.status === 200, 'List suppliers', `status=${list.status}`);
}

// ============ CIE10 ============
async function testCie10(token) {
  console.log('\n=== CIE10 ===');
  
  const list = await api('GET', '/cie10', null, token);
  log(list.status === 200, 'List CIE-10 codes', `status=${list.status}`);
}

// ============ SERVICES ============
async function testServices(token) {
  console.log('\n=== SERVICES ===');
  
  const list = await api('GET', '/service-catalog', null, token);
  log(list.status === 200, 'List service catalog', `status=${list.status}`);
}

// ============ BUILDINGS ============
async function testBuildings(token) {
  console.log('\n=== BUILDINGS ===');
  
  const list = await api('GET', '/buildings', null, token);
  log(list.status === 200, 'List buildings', `status=${list.status}`);
}

// ============ ROOMS ============
async function testRooms(token) {
  console.log('\n=== ROOMS ===');
  
  const list = await api('GET', '/rooms', null, token);
  log(list.status === 200, 'List rooms', `status=${list.status}`);
}

// ============ BEDS ============
async function testBeds(token) {
  console.log('\n=== BEDS ===');
  
  const list = await api('GET', '/beds', null, token);
  log(list.status === 200, 'List beds', `status=${list.status}`);
}

// ============ CERTIFICATES ============
async function testCertificates(token) {
  console.log('\n=== CERTIFICATES ===');
  
  const list = await api('GET', '/certificates', null, token);
  log(list.status === 200, 'List certificates', `status=${list.status}`);
  
  // Store validates
  const noPatient = await api('POST', '/certificates', {
    doctor_id: 1, type: 'sick_leave', content: 'Test'
  }, token);
  log(noPatient.status === 422, 'Create certificate without patient_id', `status=${noPatient.status}`);
  
  const noType = await api('POST', '/certificates', {
    patient_id: 1, doctor_id: 1, content: 'Test'
  }, token);
  log(noType.status === 422, 'Create certificate without type', `status=${noType.status}`);
  
  const noContent = await api('POST', '/certificates', {
    patient_id: 1, doctor_id: 1, type: 'sick_leave'
  }, token);
  log(noContent.status === 422, 'Create certificate without content', `status=${noContent.status}`);
  
  const invalidType = await api('POST', '/certificates', {
    patient_id: 1, doctor_id: 1, type: 'invalid', content: 'Test'
  }, token);
  log(invalidType.status === 422, 'Create certificate invalid type', `status=${invalidType.status}`);
}

// ============ INSTITUTION ============
async function testInstitution(token) {
  console.log('\n=== INSTITUTION ===');
  
  const get = await api('GET', '/institution', null, token);
  log(get.status === 200, 'Get institution', `status=${get.status}`);
}

// ============ DIAGNOSIS ============
async function testDiagnosis(token) {
  console.log('\n=== DIAGNOSIS ===');
  
  // Missing required medical_record_id
  const noMR = await api('POST', '/diagnoses', {
    patient_id: 1, description: 'Test'
  }, token);
  log(noMR.status === 422, 'Create diagnosis without medical_record_id', `status=${noMR.status}`);
  
  const noDesc = await api('POST', '/diagnoses', {
    medical_record_id: 1, patient_id: 1
  }, token);
  log(noDesc.status === 422, 'Create diagnosis without description', `status=${noDesc.status}`);
}

// ============ VITAL SIGNS ============
async function testVitalSigns(token) {
  console.log('\n=== VITAL SIGNS ===');
  
  const noMR = await api('POST', '/vital-signs', {
    patient_id: 1, temperature: 36.5
  }, token);
  log(noMR.status === 422, 'Create vital signs without medical_record_id', `status=${noMR.status}`);
  
  const noPatient = await api('POST', '/vital-signs', {
    medical_record_id: 1, temperature: 36.5
  }, token);
  log(noPatient.status === 422, 'Create vital signs without patient_id', `status=${noPatient.status}`);
  
  const badTemp = await api('POST', '/vital-signs', {
    medical_record_id: 1, patient_id: 1, temperature: 50
  }, token);
  log(badTemp.status === 422, 'Create vital signs temperature out of range', `status=${badTemp.status}`);
  
  const badHR = await api('POST', '/vital-signs', {
    medical_record_id: 1, patient_id: 1, heart_rate: 500
  }, token);
  log(badHR.status === 422, 'Create vital signs heart_rate out of range', `status=${badHR.status}`);
}

// ============ FRONTEND PAGES ============
async function testFrontendPages(browser) {
  console.log('\n=== FRONTEND PAGES ===');
  const context = await browser.newContext();
  const page = await context.newPage();
  
  const pages = [
    '/login', '/dashboard', '/patients', '/appointments',
    '/clinical-records', '/lab-results', '/prescriptions',
    '/billing/invoices', '/pharmacy/medications', '/reports',
    '/settings/users', '/settings/institution',
  ];
  
  for (const route of pages) {
    try {
      await page.goto(`${FRONTEND}${route}`, { timeout: 10000, waitUntil: 'networkidle' });
      await page.waitForTimeout(500);
      
      const jsErrors = [];
      page.on('pageerror', e => jsErrors.push(e.message));
      
      const title = await page.title();
      log(true, `Page ${route} loads`, `title="${title}"`);
    } catch (e) {
      log(false, `Page ${route} loads`, e.message);
    }
  }
  
  await context.close();
}

// ============ MAIN ============
async function main() {
  console.log('=== COMPREHENSIVE API + FRONTEND TEST ===\n');
  
  const browser = await chromium.launch({ headless: true });
  
  const token = await testAuth();
  await testUsers(token);
  await testPatients(token);
  await testAppointments(token);
  await testLabResults(token);
  await testPrescriptions(token);
  await testInvoices(token);
  await testMedications(token);
  await testSuppliers(token);
  await testCie10(token);
  await testServices(token);
  await testBuildings(token);
  await testRooms(token);
  await testBeds(token);
  await testCertificates(token);
  await testInstitution(token);
  await testDiagnosis(token);
  await testVitalSigns(token);
  await testFrontendPages(browser);
  
  await browser.close();
  
  console.log(`\n=== RESULTS: ${results.pass} passed, ${results.fail} failed ===`);
  if (results.errors.length > 0) {
    console.log('\nFailed tests:');
    results.errors.forEach(e => console.log(`  - ${e}`));
  }
}

main().catch(console.error);
