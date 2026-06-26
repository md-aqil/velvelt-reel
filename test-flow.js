const puppeteer = require('puppeteer');

async function testFlow() {
    const browser = await puppeteer.launch({
        headless: false,
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });

    const page = await browser.newPage();

    // Set viewport for responsive testing
    await page.setViewport({ width: 1280, height: 800 });

    const report = [];

    try {
        // Step 1: Navigate to login page
        console.log('=== Step 1: Navigating to login page ===');
        report.push('## Step 1: Login Page');
        await page.goto('http://localhost:10019/membership-login/', { waitUntil: 'domcontentloaded' });

        // Wait for form to be ready
        await page.waitForSelector('form', { timeout: 5000 }).catch(() => { });

        // Get page content for analysis
        const loginFormExists = await page.$('form') !== null;
        const loginInputs = await page.$$('input');

        report.push('- Page loaded successfully');
        report.push(`- Login form exists: ${loginFormExists}`);
        report.push(`- Number of input fields: ${loginInputs.length}`);

        // Check for visible labels
        const labels = await page.evaluate(() => {
            const labelElements = document.querySelectorAll('label');
            return Array.from(labelElements).map(l => l.innerText.trim());
        });
        report.push(`- Form labels: ${labels.join(', ')}`);

        // Check for placeholder text
        const placeholders = await page.evaluate(() => {
            const inputs = document.querySelectorAll('input[placeholder]');
            return Array.from(inputs).map(i => i.placeholder);
        });
        report.push(`- Placeholders: ${placeholders.join(', ')}`);

        // Step 2: Fill in login credentials
        console.log('=== Step 2: Logging in ===');
        report.push('\n## Step 2: Login Execution');

        // Find and fill email - try multiple selectors
        const emailInput = await page.$('input[type="email"], input[name="log"], input[name="username"], input[name="email"], input[name="swpm_user_name"]');
        if (emailInput) {
            await emailInput.click({ clickCount: 3 });
            await emailInput.type('mdaqil4k@gmail.com');
            report.push('- Email field found and filled');
        } else {
            report.push('- ERROR: Email input field not found');
        }

        // Find and fill password  
        const passwordInput = await page.$('input[type="password"], input[name="pwd"], input[name="swpm_password"]');
        if (passwordInput) {
            await passwordInput.click({ clickCount: 3 });
            await passwordInput.type('Apple@123');
            report.push('- Password field found and filled');
        } else {
            report.push('- ERROR: Password input field not found');
        }

        // Find and click submit button
        const submitBtn = await page.$('button[type="submit"], input[type="submit"], .submit-btn, .btn-submit, .woocommerce-form-row button');
        if (submitBtn) {
            const btnText = await page.evaluate(el => (el.innerText || el.value || '').trim(), submitBtn);
            report.push(`- Submit button found: "${btnText}"`);

            // Click and wait for navigation
            await Promise.all([
                page.waitForNavigation({ timeout: 10000 }).catch(() => { }),
                submitBtn.click()
            ]);

            report.push('- Submit button clicked');

            // Wait a bit for any redirects
            await new Promise(resolve => setTimeout(resolve, 2000));

            // Check for errors
            const currentUrl = page.url();
            report.push(`- Current URL after login: ${currentUrl}`);

            // Check if we're still on login page (failed login) or redirected
            const isStillOnLogin = currentUrl.includes('login');
            report.push(`- Still on login page: ${isStillOnLogin}`);

            // Check for error messages
            const errorMessages = await page.evaluate(() => {
                const errors = document.querySelectorAll('.error, .alert-error, .woocommerce-error, [class*="error"], .shake');
                return Array.from(errors).map(e => e.innerText.trim()).slice(0, 3);
            });

            if (errorMessages.length > 0) {
                report.push(`- ERROR messages found: ${errorMessages.join('; ')}`);
            } else {
                report.push('- No immediate errors after login');
            }

            // Check if logged in by looking for user dashboard elements
            const userInfo = await page.evaluate(() => {
                // Look for common logged-in indicators
                const indicators = document.querySelectorAll('.user-info, .logged-in, .welcome, .username, [class*="user"], header a, .member-dashboard');
                return Array.from(indicators).map(e => e.innerText.trim()).slice(0, 5);
            });
            report.push(`- User indicators found: ${userInfo.length > 0 ? userInfo.join(', ') : 'None'}`);

        } else {
            report.push('- ERROR: Submit button not found');
        }

        // Step 3: Navigate to talent page
        console.log('=== Step 3: Navigating to talent page ===');
        report.push('\n## Step 3: Talent Page Navigation');
        await page.goto('http://localhost:10019/talent/', { waitUntil: 'domcontentloaded' });
        await new Promise(resolve => setTimeout(resolve, 1000));

        const talentPageTitle = await page.title();
        report.push(`- Page title: ${talentPageTitle}`);

        // Check for talent submission button/link
        const addTalentBtn = await page.evaluate(() => {
            const buttons = document.querySelectorAll('a, button');
            return Array.from(buttons)
                .filter(b => {
                    const text = b.innerText.toLowerCase();
                    return text.includes('add') || text.includes('new') || text.includes('create') || text.includes('portfolio') || b.href?.includes('submit') || b.href?.includes('new-talent');
                })
                .map(b => ({ text: b.innerText.trim(), href: b.href }));
        });
        report.push(`- Potential add talent buttons/links: ${JSON.stringify(addTalentBtn.slice(0, 5))}`);

        // Step 4: Start creating new talent - click the create portfolio link
        console.log('=== Step 4: Creating new talent ===');
        report.push('\n## Step 4: Talent Creation Flow');

        // Try to find and click a "Create Portfolio" link
        const createLink = await page.$('a[href*="submit"], a[href*="talent-new"], a[href*="new-talent"], .create-portfolio, .btn-create');
        if (createLink) {
            const href = await page.evaluate(el => el.href, createLink);
            report.push(`- Found create link: ${href}`);
            await Promise.all([
                page.waitForNavigation({ timeout: 10000 }).catch(() => { }),
                createLink.click()
            ]);
            await new Promise(resolve => setTimeout(resolve, 2000));
            report.push('- Navigated to talent submission page');
        } else {
            // Try direct URL - go to talent-registration page only
            report.push('- No create link found, trying direct URL');
            await page.goto('http://localhost:10019/talent-registration/').catch(() => { });
            await new Promise(resolve => setTimeout(resolve, 2000));
        }

        const submissionUrl = page.url();
        report.push(`- Submission page URL: ${submissionUrl}`);

        // Analyze the submission form in detail
        const formAnalysis = await page.evaluate(() => {
            const forms = document.querySelectorAll('form');
            const result = {
                formsCount: forms.length,
                fields: [],
                hasNameField: false,
                hasPhotoUpload: false,
                hasBio: false,
                hasCategory: false
            };

            if (forms.length > 0) {
                const form = forms[0];
                const inputs = form.querySelectorAll('input, select, textarea');
                result.fields = Array.from(inputs).map(i => ({
                    name: i.name || i.id || 'unnamed',
                    type: i.type || 'select' || 'textarea',
                    visible: i.offsetParent !== null,
                    required: i.required || i.hasAttribute('required')
                }));

                // Check for specific field types
                result.hasNameField = Array.from(inputs).some(i => i.name?.toLowerCase().includes('name') || i.id?.toLowerCase().includes('name'));
                result.hasPhotoUpload = Array.from(inputs).some(i => i.type === 'file' || i.name?.toLowerCase().includes('photo') || i.name?.toLowerCase().includes('image'));
                result.hasBio = Array.from(inputs).some(i => i.name?.toLowerCase().includes('bio') || i.name?.toLowerCase().includes('description') || i.tagName === 'TEXTAREA');
                result.hasCategory = Array.from(inputs).some(i => i.name?.toLowerCase().includes('category') || i.name?.toLowerCase().includes('role') || i.name?.toLowerCase().includes('type'));
            }

            return result;
        });

        report.push(`- Number of forms: ${formAnalysis.formsCount}`);
        report.push(`- Form fields found: ${formAnalysis.fields.length}`);
        formAnalysis.fields.forEach(f => {
            if (f.visible) {
                report.push(`  - ${f.name} (${f.type}) - Required: ${f.required}`);
            }
        });
        report.push(`- Has name field: ${formAnalysis.hasNameField}`);
        report.push(`- Has photo upload: ${formAnalysis.hasPhotoUpload}`);
        report.push(`- Has bio/description: ${formAnalysis.hasBio}`);
        report.push(`- Has category/role: ${formAnalysis.hasCategory}`);

        // Check for multi-step indicators
        const stepInfo = await page.evaluate(() => {
            const steps = document.querySelectorAll('.step, .progress-step, [class*="step"], .wizard-step, .form-step');
            return {
                stepCount: steps.length,
                steps: Array.from(steps).map(s => s.innerText.trim()).slice(0, 5)
            };
        });
        if (stepInfo.stepCount > 0) {
            report.push(`- Multi-step form detected: ${stepInfo.steps.join(' -> ')}`);
        }

        // Check for progress bar
        const progressBar = await page.$('.progress-bar, .wizard-progress, [class*="progress"]');
        report.push(`- Progress bar found: ${progressBar !== null}`);

        // Check for error handling
        const errorElements = await page.evaluate(() => {
            const errors = document.querySelectorAll('.error, .validation-error, [class*="error"], [class*="invalid"], .alert-danger');
            return Array.from(errors).map(e => ({ text: e.innerText.trim().substring(0, 100), tag: e.tagName }));
        });
        report.push(`- Error display elements found: ${errorElements.length}`);
        if (errorElements.length > 0) {
            report.push(`- Error messages: ${errorElements.map(e => e.text).join('; ')}`);
        }

        // Check for success messages
        const successElements = await page.evaluate(() => {
            const success = document.querySelectorAll('.success, .alert-success, [class*="success"]');
            return Array.from(success).map(e => e.innerText.trim()).slice(0, 3);
        });
        report.push(`- Success messages: ${successElements.join(', ') || 'None'}`);

        // Check for help text / instructions
        const helpText = await page.evaluate(() => {
            const helps = document.querySelectorAll('.help-text, .description, .hint, [class*="help"], .form-help');
            return Array.from(helps).map(h => h.innerText.trim()).slice(0, 5);
        });
        report.push(`- Help text found: ${helpText.join(', ') || 'None'}`);

        // Step 5: Test responsive design
        console.log('=== Testing Responsive Design ===');
        report.push('\n## Step 5: Responsive Design Tests');

        // Test tablet size
        await page.setViewport({ width: 768, height: 1024 });
        await new Promise(resolve => setTimeout(resolve, 500));
        const tabletLayout = await page.evaluate(() => {
            return {
                hasHorizontalScroll: document.documentElement.scrollWidth > document.documentElement.clientWidth,
                formWidth: document.querySelector('form')?.clientWidth || 0
            };
        });
        report.push(`- Tablet (768px): Horizontal scroll = ${tabletLayout.hasHorizontalScroll}, Form width: ${tabletLayout.formWidth}px`);

        // Test mobile size
        await page.setViewport({ width: 375, height: 667 });
        await new Promise(resolve => setTimeout(resolve, 500));
        const mobileLayout = await page.evaluate(() => {
            return {
                hasHorizontalScroll: document.documentElement.scrollWidth > document.documentElement.clientWidth,
                formWidth: document.querySelector('form')?.clientWidth || 0
            };
        });
        report.push(`- Mobile (375px): Horizontal scroll = ${mobileLayout.hasHorizontalScroll}, Form width: ${mobileLayout.formWidth}px`);

        // Reset to desktop
        await page.setViewport({ width: 1280, height: 800 });

        // Step 6: Check for JavaScript errors in console
        console.log('=== Checking for JS errors ===');
        report.push('\n## Step 6: JavaScript & Console Check');

        const consoleMessages = [];
        page.on('console', msg => {
            if (msg.type() === 'error') {
                consoleMessages.push(msg.text());
            }
        });

        // Reload to capture any errors
        await page.reload({ waitUntil: 'domcontentloaded' });
        await new Promise(resolve => setTimeout(resolve, 1500));

        if (consoleMessages.length > 0) {
            report.push(`- Console errors: ${consoleMessages.join('; ')}`);
        } else {
            report.push('- No critical console errors detected');
        }

        // Step 7: Check for form validation
        console.log('=== Testing form validation ===');
        report.push('\n## Step 7: Form Validation Test');

        // Try to submit empty form
        const submitButton = await page.$('button[type="submit"], input[type="submit"], .submit-btn');
        if (submitButton) {
            const btnInfo = await page.evaluate(el => ({
                disabled: el.disabled,
                text: (el.innerText || el.value || '').trim()
            }), submitButton);
            report.push(`- Submit button: "${btnInfo.text}", disabled: ${btnInfo.disabled}`);
        }

        // Check HTML5 validation attributes
        const validationAttrs = await page.evaluate(() => {
            const inputs = document.querySelectorAll('input, select, textarea');
            return Array.from(inputs).filter(i => i.required || i.pattern || i.minLength || i.maxLength).map(i => ({
                name: i.name || i.id,
                required: i.required,
                pattern: i.pattern,
                minLength: i.minLength,
                maxLength: i.maxLength
            }));
        });
        report.push(`- Fields with validation: ${validationAttrs.length}`);

    } catch (error) {
        report.push(`\n## ERROR: ${error.message}`);
        console.error('Test error:', error);
    }

    // Print and save report
    console.log('\n' + '='.repeat(60));
    console.log('USABILITY TEST REPORT');
    console.log('='.repeat(60));
    console.log(report.join('\n'));

    const fs = require('fs');
    fs.writeFileSync('usability-report.md', report.join('\n'));
    console.log('\nReport saved to usability-report.md');

    // Keep browser open for manual review
    console.log('\n=== Test complete. Browser remains open for review. ===');
    console.log('Press Ctrl+C to close...');
}

testFlow().catch(console.error);