import { defineConfig } from 'cypress';
import createBundler from '@bahmutov/cypress-esbuild-preprocessor';
import { addCucumberPreprocessorPlugin } from '@badeball/cypress-cucumber-preprocessor';
import { createEsbuildPlugin } from '@badeball/cypress-cucumber-preprocessor/esbuild';

const baseUrl = process.env.E2E_SERVER ?? 'http://localhost:3444';

export async function setupNodeEvents(on, config) {
  // This is required for the preprocessor to be able to generate JSON reports after each run, and more,
  await addCucumberPreprocessorPlugin(on, config);

  on(
    'file:preprocessor',
    createBundler({
      plugins: [createEsbuildPlugin(config)],
    })
  );

  // Make sure to return the config object as it might have been modified by the plugin.
  return config;
}

export default defineConfig({
  e2e: {
    screenshotsFolder: '.cypress/screenshots',
    videosFolder: '.cypress/videos',
    env: {
      baseUrl,
    },
    baseUrl,
    specPattern: 'tests/e2e/**/*.feature',
    supportFile: 'tests/e2e/support.js',
    setupNodeEvents,
  },
});