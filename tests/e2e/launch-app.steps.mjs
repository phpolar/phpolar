import { When, Then } from '@badeball/cypress-cucumber-preprocessor';

When('I visit the index page', function () {
  const baseUrl = Cypress.env('baseUrl');
  cy.visit(baseUrl);
});

Then('I should be on the people list page', function () {
  cy.title().should('eq',"MyApp People List");
});

