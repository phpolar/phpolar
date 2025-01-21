import {Before, Step, Then} from '@badeball/cypress-cucumber-preprocessor';

const baseUrl = Cypress.env('baseUrl');

Then('I should not see the person in the people list', function () {
  cy.visit(baseUrl).then(() => {
    cy.title().should('eq',"MyApp People List");
    cy.contains('No results');
  });
});

Before({ tags: '@deletePerson' }, function () {
  Step(this, 'I delete a person');
});
