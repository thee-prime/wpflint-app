import { defineConfig, devices } from '@playwright/test';
import { config } from 'dotenv';

config({ path: '.env.testing' });

export default defineConfig({
    testDir: './tests/Feature',
    timeout: 30_000,
    retries: 0,
    reporter: 'html',
    use: {
        baseURL: process.env.BASE_URL || 'http://localhost',
        screenshot: 'only-on-failure',
        video: 'retain-on-failure',
    },
    projects: [
        {
            name: 'chromium',
            use: { ...devices['Desktop Chrome'] },
        },
    ],
});
