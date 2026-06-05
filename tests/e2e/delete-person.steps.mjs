import { Before, Step, Then } from '@badeball/cypress-cucumber-preprocessor';

Then('I should not see the person in the people list', function () {
  cy.visit('/').then(() => {
    cy.title().should('eq', "MyApp People List");
    cy.contains('No results');
  });
});

Before({ tags: '@deletePerson' }, function () {
  Step(this, 'I delete a person');
});
